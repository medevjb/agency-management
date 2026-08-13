<?php

namespace App\Services;

use App\Models\GithubAccount;
use App\Models\GithubRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.github.base_url');
    }

    /**
     * List repositories accessible to the given account, most recently pushed first.
     *
     * @return array<int, array{full_name: string, name: string, html_url: string, default_branch: string}>
     */
    public function listRepositories(GithubAccount $account): array
    {
        $response = Http::withToken($account->token)
            ->acceptJson()
            ->get("{$this->baseUrl}/user/repos", [
                'sort' => 'pushed',
                'per_page' => 100,
            ]);

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json())
            ->map(fn (array $repo) => [
                'full_name' => $repo['full_name'],
                'name' => $repo['name'],
                'html_url' => $repo['html_url'],
                'default_branch' => $repo['default_branch'],
            ])
            ->all();
    }

    /**
     * List a repository's branches along with each branch's latest commit,
     * most recently committed branch first. Cached briefly to respect rate limits.
     *
     * @return array<int, array{name: string, is_default: bool, commit: ?array{sha: string, message: string, author_name: ?string, author_avatar: ?string, html_url: string, committed_at: ?string}}>
     */
    public function branchesWithLatestCommit(GithubRepository $repo, int $limit = 50, bool $fresh = false): array
    {
        $cacheKey = "github:branches:{$repo->id}";

        if ($fresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($repo, $limit) {
            $response = Http::withToken($repo->account->token)
                ->acceptJson()
                ->get("{$this->baseUrl}/repos/{$repo->full_name}/branches", [
                    'per_page' => $limit,
                ]);

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json())
                ->map(fn (array $branch) => [
                    'name' => $branch['name'],
                    'is_default' => $branch['name'] === $repo->default_branch,
                    'commit' => $this->commitBySha($repo, $branch['commit']['sha']),
                ])
                ->sortByDesc(fn (array $branch) => $branch['commit']['committed_at'] ?? '')
                ->values()
                ->all();
        });
    }

    /**
     * Fetch a single commit's details by SHA. Cached for a long time since a commit's
     * own content never changes once created.
     *
     * @return array{sha: string, message: string, author_name: ?string, author_avatar: ?string, html_url: string, committed_at: ?string}|null
     */
    protected function commitBySha(GithubRepository $repo, string $sha): ?array
    {
        return Cache::remember("github:commit:{$repo->id}:{$sha}", now()->addDay(), function () use ($repo, $sha) {
            $response = Http::withToken($repo->account->token)
                ->acceptJson()
                ->get("{$this->baseUrl}/repos/{$repo->full_name}/commits/{$sha}");

            if (! $response->successful()) {
                return null;
            }

            $commit = $response->json();

            return [
                'sha' => $commit['sha'],
                'message' => $commit['commit']['message'],
                'author_name' => $commit['commit']['author']['name'] ?? $commit['author']['login'] ?? null,
                'author_avatar' => $commit['author']['avatar_url'] ?? null,
                'html_url' => $commit['html_url'],
                'committed_at' => $commit['commit']['author']['date'] ?? null,
            ];
        });
    }
}
