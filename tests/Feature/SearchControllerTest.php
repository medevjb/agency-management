<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // Teams

    public function test_teams_search_returns_json_with_default_results(): void
    {
        Team::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/search/teams');

        $response->assertOk()
            ->assertJsonStructure([['id', 'name']]);
    }

    public function test_teams_search_filters_by_query(): void
    {
        Team::factory()->create(['name' => 'Alpha Squad']);
        Team::factory()->create(['name' => 'Beta Team']);

        $response = $this->actingAs($this->user)->getJson('/search/teams?q=Alpha');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Alpha Squad']);
    }

    public function test_teams_search_requires_authentication(): void
    {
        $this->getJson('/search/teams')->assertUnauthorized();
    }

    // Tech Stacks

    public function test_tech_stacks_search_returns_json_with_default_results(): void
    {
        TechStack::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/search/tech-stacks');

        $response->assertOk()
            ->assertJsonStructure([['id', 'name']]);
    }

    public function test_tech_stacks_search_filters_by_query(): void
    {
        TechStack::factory()->create(['name' => 'React Frontend']);
        TechStack::factory()->create(['name' => 'Laravel Backend']);

        $response = $this->actingAs($this->user)->getJson('/search/tech-stacks?q=React');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'React Frontend']);
    }

    public function test_tech_stacks_search_requires_authentication(): void
    {
        $this->getJson('/search/tech-stacks')->assertUnauthorized();
    }

    // Profiles

    public function test_profiles_search_returns_json_with_id_and_formatted_name(): void
    {
        Profile::factory()->create(['username' => 'johndoe', 'platform' => 'upwork']);

        $response = $this->actingAs($this->user)->getJson('/search/profiles');

        $response->assertOk()
            ->assertJsonStructure([['id', 'name']])
            ->assertJsonFragment(['name' => 'johndoe (Upwork)']);
    }

    public function test_profiles_search_filters_by_username(): void
    {
        Profile::factory()->create(['username' => 'johndoe', 'platform' => 'upwork']);
        Profile::factory()->create(['username' => 'janedoe', 'platform' => 'fiverr']);

        $response = $this->actingAs($this->user)->getJson('/search/profiles?q=john');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'johndoe (Upwork)']);
    }

    public function test_profiles_search_filters_by_platform(): void
    {
        Profile::factory()->create(['username' => 'johndoe', 'platform' => 'upwork']);
        Profile::factory()->create(['username' => 'janedoe', 'platform' => 'fiverr']);

        $response = $this->actingAs($this->user)->getJson('/search/profiles?q=fiverr');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'janedoe (Fiverr)']);
    }

    public function test_profiles_search_requires_authentication(): void
    {
        $this->getJson('/search/profiles')->assertUnauthorized();
    }

    // Result limits

    public function test_search_results_are_limited_to_ten(): void
    {
        Team::factory()->count(15)->create();

        $response = $this->actingAs($this->user)->getJson('/search/teams');

        $response->assertOk()
            ->assertJsonCount(10);
    }
}
