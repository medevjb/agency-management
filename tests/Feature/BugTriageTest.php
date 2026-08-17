<?php

namespace Tests\Feature;

use Anthropic\Client;
use Anthropic\RequestOptions;
use App\Jobs\TriageBugReportJob;
use App\Models\BugCategory;
use App\Models\BugPriority;
use App\Models\BugReport;
use App\Models\User;
use App\Services\BugTriageService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BugTriageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected BugCategory $category;

    protected BugPriority $priority;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        $this->category = BugCategory::create([
            'name' => 'Frontend',
            'slug' => 'frontend',
        ]);
        $this->priority = BugPriority::create([
            'name' => 'High',
            'slug' => 'high',
            'color' => '#ff0000',
            'level' => 3,
        ]);
    }

    public function test_submitting_without_category_or_priority_dispatches_triage_job(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->user)->post(route('bug-reports.store'), [
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
        ]);

        $bugReport = BugReport::first();

        $this->assertRedirectToModel($response, 'bug-reports', $bugReport);
        $this->assertNull($bugReport->bug_category_id);
        $this->assertNull($bugReport->bug_priority_id);
        Queue::assertPushed(TriageBugReportJob::class, fn ($job) => $job->bugReport->id === $bugReport->id);
    }

    public function test_submitting_with_explicit_category_and_priority_skips_triage_job(): void
    {
        Queue::fake();

        $this->actingAs($this->user)->post(route('bug-reports.store'), [
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
            'bug_category_id' => $this->category->id,
            'bug_priority_id' => $this->priority->id,
        ]);

        Queue::assertNotPushed(TriageBugReportJob::class);
    }

    public function test_category_and_priority_are_required_when_ai_is_not_configured(): void
    {
        config(['services.anthropic.api_key' => null]);
        Queue::fake();

        $response = $this->actingAs($this->user)->post(route('bug-reports.store'), [
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
        ]);

        $response->assertSessionHasErrors(['bug_category_id', 'bug_priority_id']);
        $this->assertDatabaseCount('bug_reports', 0);
        Queue::assertNotPushed(TriageBugReportJob::class);
    }

    public function test_submitting_with_category_and_priority_works_when_ai_is_not_configured(): void
    {
        config(['services.anthropic.api_key' => null]);
        Queue::fake();

        $response = $this->actingAs($this->user)->post(route('bug-reports.store'), [
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
            'bug_category_id' => $this->category->id,
            'bug_priority_id' => $this->priority->id,
        ]);

        $bugReport = BugReport::first();

        $this->assertRedirectToModel($response, 'bug-reports', $bugReport);
        $this->assertEquals($this->category->id, $bugReport->bug_category_id);
        $this->assertEquals($this->priority->id, $bugReport->bug_priority_id);
        Queue::assertNotPushed(TriageBugReportJob::class);
    }

    public function test_triage_service_classifies_report_from_claude_response(): void
    {
        $bugReport = BugReport::create([
            'user_id' => $this->user->id,
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
            'status' => 'pending',
        ]);

        $this->bindFakeAnthropicTransport(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id' => 'msg_test123',
            'type' => 'message',
            'role' => 'assistant',
            'model' => 'claude-opus-4-8',
            'content' => [[
                'type' => 'text',
                'text' => json_encode([
                    'category_id' => $this->category->id,
                    'priority_id' => $this->priority->id,
                    'confidence' => 87,
                    'reasoning' => 'Describes a UI readability issue on the dashboard.',
                ]),
            ]],
            'stop_reason' => 'end_turn',
            'stop_sequence' => null,
            'usage' => ['input_tokens' => 120, 'output_tokens' => 40],
        ])));

        app(BugTriageService::class)->triage($bugReport);

        $bugReport->refresh();
        $this->assertEquals($this->category->id, $bugReport->bug_category_id);
        $this->assertEquals($this->priority->id, $bugReport->bug_priority_id);
        $this->assertEquals(87, $bugReport->ai_triage_confidence);
        $this->assertNotNull($bugReport->ai_triaged_at);
    }

    public function test_triage_service_leaves_report_untriaged_when_claude_call_fails(): void
    {
        $bugReport = BugReport::create([
            'user_id' => $this->user->id,
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
            'status' => 'pending',
        ]);

        $this->bindFakeAnthropicTransport(new Response(500, ['Content-Type' => 'application/json'], json_encode([
            'type' => 'error',
            'error' => ['type' => 'api_error', 'message' => 'Service unavailable'],
        ])));

        app(BugTriageService::class)->triage($bugReport);

        $bugReport->refresh();
        $this->assertNull($bugReport->bug_category_id);
        $this->assertNull($bugReport->bug_priority_id);
        $this->assertNull($bugReport->ai_triaged_at);
    }

    public function test_triage_service_rejects_out_of_range_ids_from_claude(): void
    {
        $bugReport = BugReport::create([
            'user_id' => $this->user->id,
            'title' => 'Dark Mode Needed',
            'description' => 'The dashboard is unreadable at night.',
            'status' => 'pending',
        ]);

        $this->bindFakeAnthropicTransport(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id' => 'msg_test123',
            'type' => 'message',
            'role' => 'assistant',
            'model' => 'claude-opus-4-8',
            'content' => [[
                'type' => 'text',
                'text' => json_encode([
                    'category_id' => $this->category->id + 999,
                    'priority_id' => $this->priority->id,
                    'confidence' => 90,
                    'reasoning' => 'Invalid category id.',
                ]),
            ]],
            'stop_reason' => 'end_turn',
            'stop_sequence' => null,
            'usage' => ['input_tokens' => 120, 'output_tokens' => 40],
        ])));

        app(BugTriageService::class)->triage($bugReport);

        $bugReport->refresh();
        $this->assertNull($bugReport->bug_category_id);
        $this->assertNull($bugReport->ai_triaged_at);
    }

    /**
     * Bind an Anthropic\Client backed by a fake PSR-18 transport so no real
     * network call happens. MessagesService is final and can't be mocked
     * directly, so we fake at the HTTP layer instead and let the real SDK
     * parse the response.
     */
    private function bindFakeAnthropicTransport(ResponseInterface $response): void
    {
        $transport = new class($response) implements ClientInterface
        {
            public function __construct(private ResponseInterface $response) {}

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };

        $client = new Client(
            apiKey: 'test-key',
            requestOptions: RequestOptions::with(transporter: $transport),
        );

        $this->app->instance(Client::class, $client);
    }
}
