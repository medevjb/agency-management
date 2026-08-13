<?php

namespace Tests\Feature;

use App\Models\GithubAccount;
use App\Models\GithubRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GitHubIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'team-member'] as $role) {
            Role::create(['name' => $role]);
        }
    }

    protected function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_admin_can_view_the_github_commits_page(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.github.index'));

        $response->assertOk();
    }

    public function test_manager_can_view_the_github_commits_page(): void
    {
        $manager = $this->userWithRole('operation-manager');

        $response = $this->actingAs($manager)->get(route('admin.github.index'));

        $response->assertOk();
    }

    public function test_team_member_cannot_view_the_github_commits_page(): void
    {
        $teamMember = $this->userWithRole('team-member');

        $response = $this->actingAs($teamMember)->get(route('admin.github.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_initiate_github_oauth_connect(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.github.connect'));

        $response->assertRedirect();
        $this->assertStringContainsString('github.com', $response->headers->get('Location'));
    }

    public function test_non_admin_cannot_initiate_github_oauth_connect(): void
    {
        $manager = $this->userWithRole('operation-manager');

        $response = $this->actingAs($manager)->get(route('admin.github.connect'));

        $response->assertForbidden();
    }

    protected function seedConnectToken(int $adminId): string
    {
        $token = 'test-connect-token-'.$adminId.'-'.uniqid();

        Cache::put("github-connect:{$token}", $adminId, now()->addMinutes(30));

        return $token;
    }

    public function test_callback_creates_a_github_account_from_the_authorized_user(): void
    {
        $admin = $this->userWithRole('admin');
        $token = $this->seedConnectToken($admin->id);

        Socialite::shouldReceive('driver->stateless->user')->once()->andReturn(SocialiteUser::fake([
            'nickname' => 'octocat',
            'name' => 'The Octocat',
            'avatar' => 'https://avatars.githubusercontent.com/u/1',
            'token' => 'gho_oauthtoken123',
        ]));

        $response = $this->actingAs($admin)->get(route('admin.github.callback', ['state' => $token]));

        $response->assertRedirect(route('admin.github.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('github_accounts', 1);

        $account = GithubAccount::first();
        $this->assertSame('octocat', $account->username);
        $this->assertSame('gho_oauthtoken123', $account->token);
        $this->assertSame($admin->id, $account->created_by);

        $rawToken = DB::table('github_accounts')->value('token');
        $this->assertNotSame('gho_oauthtoken123', $rawToken);
    }

    public function test_callback_can_complete_in_a_browser_with_no_pmt_session(): void
    {
        $admin = $this->userWithRole('admin');
        $token = $this->seedConnectToken($admin->id);

        Socialite::shouldReceive('driver->stateless->user')->once()->andReturn(SocialiteUser::fake([
            'nickname' => 'octocat',
            'name' => 'The Octocat',
            'avatar' => 'https://avatars.githubusercontent.com/u/1',
            'token' => 'gho_oauthtoken123',
        ]));

        $response = $this->get(route('admin.github.callback', ['state' => $token]));

        $response->assertOk();
        $response->assertSee('GitHub account connected');

        $account = GithubAccount::first();
        $this->assertSame('octocat', $account->username);
        $this->assertSame($admin->id, $account->created_by);
    }

    public function test_callback_updates_the_existing_account_on_reconnect(): void
    {
        $account = GithubAccount::factory()->create(['username' => 'octocat', 'token' => 'gho_oldtoken']);
        $admin = $this->userWithRole('admin');
        $token = $this->seedConnectToken($admin->id);

        Socialite::shouldReceive('driver->stateless->user')->once()->andReturn(SocialiteUser::fake([
            'nickname' => 'octocat',
            'name' => 'The Octocat',
            'avatar' => 'https://avatars.githubusercontent.com/u/1',
            'token' => 'gho_newtoken456',
        ]));

        $response = $this->actingAs($admin)->get(route('admin.github.callback', ['state' => $token]));

        $response->assertRedirect(route('admin.github.index'));
        $this->assertDatabaseCount('github_accounts', 1);
        $this->assertSame('gho_newtoken456', $account->fresh()->token);
    }

    public function test_callback_handles_authorization_failure_gracefully(): void
    {
        $admin = $this->userWithRole('admin');
        $token = $this->seedConnectToken($admin->id);

        Socialite::shouldReceive('driver->stateless->user')->once()->andThrow(new \Exception('invalid state'));

        $response = $this->actingAs($admin)->get(route('admin.github.callback', ['state' => $token]));

        $response->assertRedirect(route('admin.github.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('github_accounts', 0);
    }

    public function test_callback_rejects_a_missing_or_expired_token(): void
    {
        $response = $this->get(route('admin.github.callback', ['state' => 'not-a-real-token']));

        $response->assertOk();
        $response->assertSee('expired or was already used');
        $this->assertDatabaseCount('github_accounts', 0);
    }

    public function test_callback_token_can_only_be_used_once(): void
    {
        $admin = $this->userWithRole('admin');
        $token = $this->seedConnectToken($admin->id);

        Socialite::shouldReceive('driver->stateless->user')->once()->andReturn(SocialiteUser::fake([
            'nickname' => 'octocat',
            'token' => 'gho_oauthtoken123',
        ]));

        $this->get(route('admin.github.callback', ['state' => $token]));
        $second = $this->get(route('admin.github.callback', ['state' => $token]));

        $second->assertSee('expired or was already used');
        $this->assertDatabaseCount('github_accounts', 1);
    }

    public function test_non_admin_cannot_remove_a_github_account(): void
    {
        $account = GithubAccount::factory()->create();
        $manager = $this->userWithRole('operation-manager');

        $response = $this->actingAs($manager)->delete(route('admin.github.accounts.destroy', $account));

        $response->assertForbidden();
        $this->assertDatabaseHas('github_accounts', ['id' => $account->id]);
    }

    public function test_admin_can_sync_tracked_repositories_for_an_account(): void
    {
        $account = GithubAccount::factory()->create();
        GithubRepository::factory()->create(['github_account_id' => $account->id, 'full_name' => 'octocat/old-repo']);

        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->putJson(route('admin.github.repositories.sync', $account), [
            'repositories' => [
                ['full_name' => 'octocat/hello-world', 'name' => 'hello-world', 'html_url' => 'https://github.com/octocat/hello-world', 'default_branch' => 'main'],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('github_repositories', 1);
        $this->assertDatabaseHas('github_repositories', [
            'github_account_id' => $account->id,
            'full_name' => 'octocat/hello-world',
        ]);
        $this->assertDatabaseMissing('github_repositories', ['full_name' => 'octocat/old-repo']);
    }

    protected function fakeGitHubBranchesAndCommits(): GithubRepository
    {
        $account = GithubAccount::factory()->create();
        $repo = GithubRepository::factory()->create([
            'github_account_id' => $account->id,
            'full_name' => 'octocat/hello-world',
            'default_branch' => 'main',
        ]);

        Http::fake([
            'api.github.com/repos/octocat/hello-world/branches*' => Http::response([
                ['name' => 'main', 'commit' => ['sha' => 'aaa111']],
                ['name' => 'feature/x', 'commit' => ['sha' => 'bbb222']],
            ], 200),
            'api.github.com/repos/octocat/hello-world/commits/aaa111' => Http::response([
                'sha' => 'aaa111',
                'html_url' => 'https://github.com/octocat/hello-world/commit/aaa111',
                'commit' => ['message' => 'main commit', 'author' => ['name' => 'Octo Cat', 'date' => '2026-01-01T00:00:00Z']],
                'author' => ['avatar_url' => 'https://avatars.githubusercontent.com/u/1'],
            ], 200),
            'api.github.com/repos/octocat/hello-world/commits/bbb222' => Http::response([
                'sha' => 'bbb222',
                'html_url' => 'https://github.com/octocat/hello-world/commit/bbb222',
                'commit' => ['message' => 'feature commit', 'author' => ['name' => 'Octo Cat', 'date' => '2026-06-01T00:00:00Z']],
                'author' => ['avatar_url' => 'https://avatars.githubusercontent.com/u/1'],
            ], 200),
        ]);

        return $repo;
    }

    public function test_branches_endpoint_returns_branches_with_latest_commit_newest_first(): void
    {
        $repo = $this->fakeGitHubBranchesAndCommits();
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->getJson(route('admin.github.branches', $repo));

        $response->assertOk();
        $branches = $response->json('branches');

        $this->assertCount(2, $branches);
        $this->assertSame('feature/x', $branches[0]['name']);
        $this->assertSame('bbb222', $branches[0]['commit']['sha']);
        $this->assertFalse($branches[0]['is_default']);
        $this->assertSame('main', $branches[1]['name']);
        $this->assertTrue($branches[1]['is_default']);
    }

    public function test_manager_can_view_branches(): void
    {
        $repo = $this->fakeGitHubBranchesAndCommits();
        $manager = $this->userWithRole('operation-manager');

        $response = $this->actingAs($manager)->getJson(route('admin.github.branches', $repo));

        $response->assertOk();
        $this->assertCount(2, $response->json('branches'));
    }

    public function test_team_member_cannot_view_branches(): void
    {
        $repo = $this->fakeGitHubBranchesAndCommits();
        $teamMember = $this->userWithRole('team-member');

        $response = $this->actingAs($teamMember)->getJson(route('admin.github.branches', $repo));

        $response->assertForbidden();
    }
}
