<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function defaultResultsProvider(): array
    {
        return [
            'teams' => ['/search/teams', fn () => Team::factory()->count(3)->create()],
            'tech-stacks' => ['/search/tech-stacks', fn () => TechStack::factory()->count(3)->create()],
            'profiles' => ['/search/profiles', fn () => Profile::factory()->create(['username' => 'johndoe', 'platform' => 'upwork'])],
        ];
    }

    #[DataProvider('defaultResultsProvider')]
    public function test_search_returns_json_with_default_results(string $url, \Closure $seed): void
    {
        $seed();

        $this->actingAs($this->user)->getJson($url)
            ->assertOk()
            ->assertJsonStructure([['id', 'name']]);
    }

    public static function queryFilterProvider(): array
    {
        return [
            'teams' => [
                '/search/teams?q=Alpha',
                fn () => [Team::factory()->create(['name' => 'Alpha Squad']), Team::factory()->create(['name' => 'Beta Team'])],
                'Alpha Squad',
            ],
            'tech-stacks' => [
                '/search/tech-stacks?q=React',
                fn () => [TechStack::factory()->create(['name' => 'React Frontend']), TechStack::factory()->create(['name' => 'Laravel Backend'])],
                'React Frontend',
            ],
            'profiles' => [
                '/search/profiles?q=john',
                fn () => [Profile::factory()->create(['username' => 'johndoe', 'platform' => 'upwork']), Profile::factory()->create(['username' => 'janedoe', 'platform' => 'fiverr'])],
                'johndoe (Upwork)',
            ],
        ];
    }

    #[DataProvider('queryFilterProvider')]
    public function test_search_filters_by_query(string $url, \Closure $seed, string $expectedName): void
    {
        $seed();

        $this->actingAs($this->user)->getJson($url)
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => $expectedName]);
    }

    public function test_profiles_search_filters_by_platform(): void
    {
        Profile::factory()->create(['username' => 'johndoe', 'platform' => 'upwork']);
        Profile::factory()->create(['username' => 'janedoe', 'platform' => 'fiverr']);

        $this->actingAs($this->user)->getJson('/search/profiles?q=fiverr')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'janedoe (Fiverr)']);
    }

    public static function authRequiredProvider(): array
    {
        return [
            'teams' => ['/search/teams'],
            'tech-stacks' => ['/search/tech-stacks'],
            'profiles' => ['/search/profiles'],
        ];
    }

    #[DataProvider('authRequiredProvider')]
    public function test_search_requires_authentication(string $url): void
    {
        $this->getJson($url)->assertUnauthorized();
    }

    public function test_search_results_are_limited_to_ten(): void
    {
        Team::factory()->count(15)->create();

        $this->actingAs($this->user)->getJson('/search/teams')
            ->assertOk()
            ->assertJsonCount(10);
    }
}
