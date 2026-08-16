<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_the_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('command center', false);
        $response->assertSee('Fiverr', false);
        $response->assertSee('Upwork', false);
    }

    public function test_authenticated_user_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
