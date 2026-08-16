<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderAudit;
use App\Models\OrderValueDistribution;
use App\Models\Profile;
use App\Models\Team;
use App\Models\TechStack;
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

    public function test_a_new_order_defaults_to_nra_status_and_zero_progress(): void
    {
        $this->actingAs($this->user)->post(route('orders.store'), [
            'name' => 'Acme Corporation',
            'priority' => 'medium',
        ])->assertRedirect(route('orders.index'));

        $order = Order::first();
        $this->assertSame('nra', $order->status);
        $this->assertSame(0, $order->progress);
    }

    public function test_the_create_form_omits_the_workflow_section_and_shows_priority_up_top(): void
    {
        $response = $this->actingAs($this->user)->get(route('orders.create'))->assertOk();

        $this->assertStringNotContainsString('name="status"', $response->getContent());
        $this->assertStringNotContainsString('name="progress"', $response->getContent());
        $this->assertStringContainsString('name="priority"', $response->getContent());
    }

    public function test_the_edit_form_still_shows_the_workflow_section(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('orders.edit', $order))->assertOk();

        $this->assertStringContainsString('name="status"', $response->getContent());
        $this->assertStringContainsString('name="progress"', $response->getContent());
    }

    public function test_user_search_includes_team_and_tech_stack(): void
    {
        $team = Team::factory()->create(['name' => 'Frontend Guild']);
        $stack = TechStack::factory()->create(['name' => 'Vue']);
        User::factory()->create(['name' => 'Dev Team Person', 'team_id' => $team->id, 'tech_stack_id' => $stack->id]);

        $response = $this->actingAs($this->user)->getJson(route('orders.search-users', ['search' => 'Dev Team Person']))
            ->assertOk();

        $this->assertSame('Frontend Guild', $response->json('users.0.team.name'));
        $this->assertSame('Vue', $response->json('users.0.tech_stack.name'));
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
            ->assertSee('1–15')
            ->assertSee('auditPager');

        // The list markup only; the script block also mentions the attribute.
        $this->assertSame(15, substr_count($response->getContent(), '<article class="log-entry"'));
    }

    public function test_change_log_endpoint_reports_pagination(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        for ($i = 1; $i <= 20; $i++) {
            $order->update(['progress' => $i]);
        }

        $first = $this->actingAs($this->user)->getJson(route('orders.audits', $order))
            ->assertOk()
            ->assertJson(['current_page' => 1, 'last_page' => 2, 'total' => 21, 'from' => 1, 'to' => 15])
            ->json();

        $this->assertSame(15, substr_count($first['html'], '<article class="log-entry"'));

        $second = $this->actingAs($this->user)->getJson(route('orders.audits', [$order, 'page' => 2]))
            ->assertOk()
            ->assertJson(['current_page' => 2, 'last_page' => 2, 'from' => 16, 'to' => 21])
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

    public function test_a_team_member_assigned_to_an_order_can_update_its_progress(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id, 'progress' => 10]);
        $order->users()->attach($member->id);

        $this->actingAs($member)->postJson(route('orders.quick-update', $order), [
            'progress' => 75,
        ])->assertOk();

        $this->assertSame(75, $order->fresh()->progress);
    }

    public function test_the_show_page_offers_the_progress_editor_to_an_assigned_team_member(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id]);
        $order->users()->attach($member->id);

        $this->actingAs($member)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('id="progressEditToggle"', false);
    }

    public function test_a_team_member_not_assigned_to_an_order_cannot_update_its_progress(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id, 'progress' => 10]);

        $this->actingAs($member)->postJson(route('orders.quick-update', $order), [
            'progress' => 75,
        ])->assertForbidden();

        $this->assertSame(10, $order->fresh()->progress);
    }

    public function test_an_assigned_team_member_still_cannot_change_other_fields_via_quick_update(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id, 'status' => 'in-progress']);
        $order->users()->attach($member->id);

        $this->actingAs($member)->postJson(route('orders.quick-update', $order), [
            'status' => 'completed',
        ])->assertForbidden();

        $this->assertSame('in-progress', $order->fresh()->status);
    }

    public function test_an_assigned_team_member_can_edit_notes_and_credentials(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id]);
        $order->users()->attach($member->id);

        $this->actingAs($member)->postJson(route('orders.quick-update', $order), [
            'notes' => 'Client wants a weekly recap.',
        ])->assertOk();

        $this->assertSame('Client wants a weekly recap.', $order->fresh()->notes);
    }

    public function test_an_assigned_team_member_can_upload_but_not_delete_documents(): void
    {
        Storage::fake(config('filesystems.default'));
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id]);
        $order->users()->attach($member->id);

        $this->actingAs($member)->post(route('orders.documents.store', $order), [
            'client_documents' => [UploadedFile::fake()->create('brief.pdf', 30, 'application/pdf')],
        ])->assertRedirect();

        $document = $order->fresh()->documents()[0];
        $this->assertSame('brief.pdf', $document['name']);

        $this->actingAs($member)
            ->deleteJson(route('orders.documents.destroy', [$order, $document['id']]))
            ->assertForbidden();

        $this->assertCount(1, $order->fresh()->documents());
    }

    public function test_a_team_member_not_assigned_cannot_edit_content(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($member)->postJson(route('orders.quick-update', $order), [
            'notes' => 'Should not land.',
        ])->assertForbidden();

        $this->assertNull($order->fresh()->notes);
    }

    public function test_the_details_page_offers_inline_document_upload_to_an_assigned_team_member(): void
    {
        Role::create(['name' => 'team-member']);
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $order = Order::factory()->create(['created_by' => $this->user->id]);
        $order->users()->attach($member->id);

        $this->actingAs($member)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('id="inlineDocDropzone"', false);
    }

    public function test_a_stack_leader_can_update_their_teams_orders_but_not_others(): void
    {
        Role::create(['name' => 'stack-leader']);
        $team = Team::factory()->create();
        $leader = User::factory()->create(['team_id' => $team->id]);
        $leader->assignRole('stack-leader');

        $ownOrder = Order::factory()->create(['created_by' => $this->user->id, 'team_id' => $team->id]);
        $otherTeam = Team::factory()->create();
        $otherOrder = Order::factory()->create(['created_by' => $this->user->id, 'team_id' => $otherTeam->id]);

        $this->actingAs($leader)->postJson(route('orders.quick-update', $ownOrder), [
            'status' => 'completed',
        ])->assertOk();

        $this->assertSame('completed', $ownOrder->fresh()->status);

        $this->actingAs($leader)->postJson(route('orders.quick-update', $otherOrder), [
            'status' => 'completed',
        ])->assertForbidden();

        $this->assertSame('in-progress', $otherOrder->fresh()->status);
    }

    public function test_the_orders_list_offers_completed_as_a_status_option(): void
    {
        $response = $this->actingAs($this->user)->get(route('orders.index'))->assertOk();

        // The status filter dropdown and the inline quick-edit dropdown both
        // need it — count catches a regression to just one of the two.
        $this->assertSame(2, substr_count($response->getContent(), 'option value="completed"'));
    }

    public function test_the_orders_list_exposes_completed_at_alongside_delivered_at(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'in-progress',
        ]);
        $order->update(['status' => 'completed']);

        $data = $this->actingAs($this->user)->getJson(route('orders.data'))->assertOk()->json('data');

        $row = collect($data)->firstWhere('id', $order->id);
        $this->assertNotNull($row['completed_at']);
    }

    public function test_delivered_at_cannot_be_set_by_hand_but_completed_at_can_through_quick_update(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id, 'status' => 'in-progress']);
        $completedAt = now()->toDateTimeString();

        $this->actingAs($this->user)->postJson(route('orders.quick-update', $order), [
            'delivered_at' => now()->toDateTimeString(),
            'completed_at' => $completedAt,
        ])->assertOk();

        $fresh = $order->fresh();
        $this->assertNull($fresh->delivered_at);
        $this->assertSame($completedAt, $fresh->completed_at->toDateTimeString());
    }

    public function test_freelance_profile_and_order_id_can_be_changed_through_quick_update(): void
    {
        $profile = Profile::factory()->create();
        $order = Order::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)->postJson(route('orders.quick-update', $order), [
            'freelance_profile_id' => $profile->id,
            'order_id' => 'ORD-2026-001',
        ])->assertOk();

        $fresh = $order->fresh();
        $this->assertSame($profile->id, $fresh->freelance_profile_id);
        $this->assertSame('ORD-2026-001', $fresh->order_id);
    }

    public function test_a_duplicate_order_id_is_rejected_by_quick_update(): void
    {
        Order::factory()->create(['created_by' => $this->user->id, 'order_id' => 'ORD-2026-001']);
        $order = Order::factory()->create(['created_by' => $this->user->id, 'order_id' => null]);

        $this->actingAs($this->user)->postJson(route('orders.quick-update', $order), [
            'order_id' => 'ORD-2026-001',
        ])->assertStatus(422);

        $this->assertNull($order->fresh()->order_id);
    }

    public function test_an_order_id_can_be_cleared_through_quick_update(): void
    {
        $order = Order::factory()->create(['created_by' => $this->user->id, 'order_id' => 'ORD-2026-001']);

        $this->actingAs($this->user)->postJson(route('orders.quick-update', $order), [
            'order_id' => '',
        ])->assertOk();

        $this->assertNull($order->fresh()->order_id);
    }

    public function test_a_completed_order_with_no_value_distribution_is_flagged_as_needing_one(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'in-progress',
            'value' => 1000,
        ]);
        $order->update(['status' => 'completed']);

        $data = $this->actingAs($this->user)->getJson(route('orders.data'))->assertOk()->json('data');
        $row = collect($data)->firstWhere('id', $order->id);

        $this->assertTrue($row['needs_value_distribution']);
    }

    public function test_a_completed_order_fully_distributed_is_not_flagged(): void
    {
        $order = Order::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'in-progress',
            'value' => 1000,
        ]);
        $order->update(['status' => 'completed']);
        OrderValueDistribution::create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => $order->fresh()->netValue(),
            'distributed_by' => $this->user->id,
        ]);

        $data = $this->actingAs($this->user)->getJson(route('orders.data'))->assertOk()->json('data');
        $row = collect($data)->firstWhere('id', $order->id);

        $this->assertFalse($row['needs_value_distribution']);
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
