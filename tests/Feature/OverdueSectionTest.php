<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OverdueSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_accordion_lists_orders_worst_first(): void
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        Order::factory()->create(['created_by' => $user->id, 'name' => 'Recent Late', 'end_date' => now()->subDays(2)]);
        Order::factory()->create(['created_by' => $user->id, 'name' => 'Very Late', 'end_date' => now()->subDays(12)]);
        Order::factory()->create(['created_by' => $user->id, 'name' => 'On Time', 'end_date' => now()->addDays(30)]);

        $response = $this->actingAs($user)->get(route('orders.index'));

        $response->assertOk()
            ->assertSee('Orders are overdue')
            ->assertSee('Worst is 12 days past deadline')
            ->assertSeeInOrder(['Very Late', 'Recent Late'])
            ->assertSee('aria-controls="overdue-detail-', false);

        $this->assertStringNotContainsString('fa-skull-crossbones', $response->getContent());
    }
}
