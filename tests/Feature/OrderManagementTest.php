<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderAudit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    /**
     * @return array<string, mixed>
     */
    protected function orderPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acme Corporation',
            'status' => 'in-progress',
            'priority' => 'medium',
        ], $overrides);
    }

    public function test_order_can_be_created_with_notes_credentials_and_progress(): void
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), $this->orderPayload([
            'notes' => 'Client prefers updates on Mondays.',
            'credentials' => '<p>cPanel: <strong>admin</strong> / secret</p>',
            'progress' => 40,
        ]));

        $response->assertRedirect(route('orders.index'));

        $order = Order::first();
        $this->assertSame('Client prefers updates on Mondays.', $order->notes);
        $this->assertSame('<p>cPanel: <strong>admin</strong> / secret</p>', $order->credentials);
        $this->assertSame(40, $order->progress);
    }

    public function test_credentials_are_encrypted_at_rest(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'credentials' => '<p>super-secret-password</p>',
        ]);

        $stored = DB::table('orders')->where('id', $order->id)->value('credentials');

        $this->assertNotSame('<p>super-secret-password</p>', $stored);
        $this->assertStringNotContainsString('super-secret-password', $stored);
        $this->assertSame('<p>super-secret-password</p>', $order->fresh()->credentials);
    }

    public function test_credentials_html_is_sanitized(): void
    {
        $this->actingAs($this->user)->post(route('orders.store'), $this->orderPayload([
            'credentials' => '<p onclick="steal()">panel</p><script>alert(1)</script>',
        ]));

        $credentials = Order::first()->credentials;

        $this->assertStringNotContainsString('<script', $credentials);
        $this->assertStringNotContainsString('onclick', $credentials);
        $this->assertStringContainsString('panel', $credentials);
    }

    public function test_empty_credentials_editor_output_is_stored_as_null(): void
    {
        $this->actingAs($this->user)->post(route('orders.store'), $this->orderPayload([
            'credentials' => '<p><br></p>',
        ]));

        $this->assertNull(Order::first()->credentials);
    }

    public function test_progress_must_be_between_zero_and_one_hundred(): void
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), $this->orderPayload([
            'progress' => 140,
        ]));

        $response->assertSessionHasErrors('progress');
        $this->assertSame(0, Order::count());
    }

    public function test_client_documents_are_uploaded_and_recorded_on_the_order(): void
    {
        Storage::fake(config('filesystems.default'));

        $this->actingAs($this->user)->post(route('orders.store'), $this->orderPayload([
            'client_documents' => [
                UploadedFile::fake()->create('brief.pdf', 120, 'application/pdf'),
                UploadedFile::fake()->create('assets.zip', 80, 'application/zip'),
            ],
        ]));

        $order = Order::first();
        $documents = $order->documents();

        $this->assertCount(2, $documents);
        $this->assertEqualsCanonicalizing(['brief.pdf', 'assets.zip'], array_column($documents, 'name'));

        foreach ($documents as $document) {
            Storage::disk($document['disk'])->assertExists($document['path']);
        }
    }

    public function test_client_document_can_be_downloaded_and_deleted(): void
    {
        Storage::fake(config('filesystems.default'));

        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)->patch(route('orders.update', $order), $this->orderPayload([
            'name' => $order->name,
            'client_documents' => [UploadedFile::fake()->create('brief.pdf', 40, 'application/pdf')],
        ]));

        $document = $order->fresh()->documents()[0];

        $this->actingAs($this->user)
            ->get(route('orders.documents.download', [$order, $document['id']]))
            ->assertOk()
            ->assertDownload('brief.pdf');

        $this->actingAs($this->user)
            ->deleteJson(route('orders.documents.destroy', [$order, $document['id']]))
            ->assertOk();

        $this->assertSame([], $order->fresh()->documents());
        Storage::disk($document['disk'])->assertMissing($document['path']);
    }

    public function test_updating_an_order_records_an_audit_entry_with_previous_values(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'pending',
            'progress' => 10,
        ]);

        $this->actingAs($this->user)->patch(route('orders.update', $order), $this->orderPayload([
            'name' => $order->name,
            'status' => 'in-progress',
            'progress' => 60,
        ]));

        $audit = OrderAudit::where('order_id', $order->id)->where('event', 'updated')->latest('id')->first();

        $this->assertNotNull($audit);
        $this->assertSame($this->user->id, $audit->user_id);
        $this->assertSame('pending', $audit->old_values['status']);
        $this->assertSame('in-progress', $audit->new_values['status']);
        $this->assertSame(10, $audit->old_values['progress']);
        $this->assertSame(60, $audit->new_values['progress']);
    }

    public function test_audit_trail_never_stores_credential_values(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)->patch(route('orders.update', $order), $this->orderPayload([
            'name' => $order->name,
            'credentials' => '<p>hunter2</p>',
        ]));

        $audit = OrderAudit::where('order_id', $order->id)->where('event', 'updated')->latest('id')->first();

        $this->assertSame('[redacted]', $audit->new_values['credentials']);
        $this->assertStringNotContainsString('hunter2', json_encode($audit->new_values));
        $this->assertSame('••••••••', collect($audit->formattedChanges())->firstWhere('field', 'credentials')['new']);
    }

    public function test_document_uploads_are_audited_separately(): void
    {
        Storage::fake(config('filesystems.default'));

        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)->patch(route('orders.update', $order), $this->orderPayload([
            'name' => $order->name,
            'client_documents' => [UploadedFile::fake()->create('scope.pdf', 20, 'application/pdf')],
        ]));

        $audit = OrderAudit::where('order_id', $order->id)->where('event', 'document_uploaded')->first();

        $this->assertNotNull($audit);
        $this->assertSame(['scope.pdf'], $audit->new_values['documents']);
    }

    public function test_member_assignment_changes_are_audited(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);
        $member = User::factory()->create();

        $this->actingAs($this->user)->postJson(route('orders.assign-users', $order), [
            'users' => [$member->id],
        ])->assertOk();

        $audit = OrderAudit::where('order_id', $order->id)->where('event', 'members_updated')->first();

        $this->assertNotNull($audit);
        $this->assertSame([$member->name], $audit->new_values['members']);
    }

    public function test_order_details_page_shows_notes_documents_and_change_log(): void
    {
        Storage::fake(config('filesystems.default'));

        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'notes' => 'Deliver the staging link first.',
            'progress' => 75,
        ]);

        $this->actingAs($this->user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Deliver the staging link first.')
            ->assertSee('75%')
            ->assertSee('Change log');
    }

    public function test_change_log_is_paginated_on_the_details_page(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        // 1 "created" audit plus 20 updates.
        for ($i = 1; $i <= 20; $i++) {
            $order->update(['progress' => $i]);
        }

        $response = $this->actingAs($this->user)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('Showing')
            ->assertSee('Load older changes');

        // The list markup only; the script block also mentions the attribute.
        $this->assertSame(15, substr_count($response->getContent(), '<article class="log-entry"'));
    }

    public function test_change_log_endpoint_returns_the_next_page(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        for ($i = 1; $i <= 20; $i++) {
            $order->update(['progress' => $i]);
        }

        $first = $this->actingAs($this->user)->getJson(route('orders.audits', $order))
            ->assertOk()
            ->assertJson(['has_more' => true, 'next_page' => 2, 'total' => 21])
            ->json();

        $this->assertSame(15, substr_count($first['html'], '<article class="log-entry"'));

        $second = $this->actingAs($this->user)->getJson(route('orders.audits', [$order, 'page' => 2]))
            ->assertOk()
            ->assertJson(['has_more' => false])
            ->json();

        $this->assertSame(6, substr_count($second['html'], '<article class="log-entry"'));
    }

    public function test_change_log_can_be_filtered_by_event_group(): void
    {
        Storage::fake(config('filesystems.default'));

        $order = Order::factory()->create(['created_by' => $this->user->id]);
        $member = User::factory()->create();

        $this->actingAs($this->user)->patch(route('orders.update', $order), $this->orderPayload([
            'name' => $order->name,
            'client_documents' => [UploadedFile::fake()->create('brief.pdf', 10, 'application/pdf')],
        ]));

        $this->actingAs($this->user)->postJson(route('orders.assign-users', $order), ['users' => [$member->id]]);

        $files = $this->actingAs($this->user)
            ->getJson(route('orders.audits', [$order, 'filter' => 'files']))
            ->assertOk()
            ->json();

        $this->assertSame(1, $files['total']);
        $this->assertStringContainsString('uploaded 1 file', $files['html']);

        $people = $this->actingAs($this->user)
            ->getJson(route('orders.audits', [$order, 'filter' => 'people']))
            ->assertOk()
            ->json();

        $this->assertSame(1, $people['total']);
        $this->assertStringContainsString('changed who is assigned', $people['html']);
    }

    public function test_change_log_rejects_an_unknown_filter(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson(route('orders.audits', [$order, 'filter' => 'everything']))
            ->assertStatus(422);
    }

    public function test_create_and_edit_pages_render_the_new_fields(): void
    {
        Storage::fake(config('filesystems.default'));

        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)->patch(route('orders.update', $order), $this->orderPayload([
            'name' => $order->name,
            'client_documents' => [UploadedFile::fake()->create('brief.pdf', 15, 'application/pdf')],
        ]));

        $this->actingAs($this->user)
            ->get(route('orders.create'))
            ->assertOk()
            ->assertSee('Notes')
            ->assertSee('Credentials')
            ->assertSee('Progress')
            ->assertSee('Initial Client Documents');

        $this->actingAs($this->user)
            ->get(route('orders.edit', $order))
            ->assertOk()
            ->assertSee('Initial Client Documents')
            ->assertSee('brief.pdf');
    }

    public function test_bulk_status_changes_are_audited(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user)->postJson(route('orders.bulk-action'), [
            'ids' => [$order->id],
            'action' => 'mark-on-hold',
        ])->assertOk();

        $this->assertSame('on-hold', $order->fresh()->status);

        $audit = OrderAudit::where('order_id', $order->id)->where('event', 'updated')->latest('id')->first();

        $this->assertNotNull($audit);
        $this->assertSame('pending', $audit->old_values['status']);
        $this->assertSame('on-hold', $audit->new_values['status']);
    }

    public function test_progress_can_be_changed_through_the_quick_update_endpoint(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)->postJson(route('orders.quick-update', $order), [
            'progress' => 55,
        ])->assertOk();

        $this->assertSame(55, $order->fresh()->progress);
    }

    public function test_delivering_an_order_completes_its_progress(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'in-progress',
            'progress' => 30,
        ]);

        $order->update(['status' => 'delivered']);

        $this->assertSame(100, $order->fresh()->progress);
    }
}
