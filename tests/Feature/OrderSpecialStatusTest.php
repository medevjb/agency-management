<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderSpecialStatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->user = User::factory()->create();
    }

    public function test_order_can_be_created_with_special_status(): void
    {
        $order = Order::create([
            'name' => 'Test Order',
            'status' => 'pending',
            'priority' => 'medium',
            'special_status' => 'selected_for_delivery',
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals('selected_for_delivery', $order->special_status->value);
        $this->assertEquals('Selected for delivery', $order->special_status->getLabel());
    }

    public function test_order_special_status_can_be_updated(): void
    {
        $order = Order::create([
            'name' => 'Test Order',
            'status' => 'pending',
            'priority' => 'medium',
            'special_status' => 'selected_for_delivery',
            'created_by' => $this->user->id,
        ]);

        $order->update(['special_status' => 'stuck_order']);

        $this->assertEquals('stuck_order', $order->fresh()->special_status->value);
        $this->assertEquals('Stuck order', $order->fresh()->special_status->getLabel());
    }

    public function test_order_can_have_null_special_status(): void
    {
        $order = Order::create([
            'name' => 'Test Order',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $this->user->id,
        ]);

        $this->assertNull($order->special_status);
    }

    public function test_special_status_enum_returns_correct_colors(): void
    {
        $this->assertEquals('bg-green-100 text-green-800', \App\SpecialStatus::SelectedForDelivery->getColor());
        $this->assertEquals('bg-blue-100 text-blue-800', \App\SpecialStatus::ContinueForNext->getColor());
        $this->assertEquals('bg-red-100 text-red-800', \App\SpecialStatus::StuckOrder->getColor());
    }
}
