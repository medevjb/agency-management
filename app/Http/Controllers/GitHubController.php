<?php

namespace App\Http\Controllers;

use App\Models\GithubAccount;
use App\Models\GithubRepository;
use App\Services\GitHubService;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    protected const CONNECT_SCOPES = ['repo', 'read:user'];

    protected const CONNECT_TOKEN_TTL_MINUTES = 30;

    public function __construct(protected GitHubService $github) {}

    /**
     * Display the GitHub commits page.
     */
    public function index(Request $request): View
    {
        $accounts = GithubAccount::with('repositories')->latest()->get();

        $connectUrl = $request->user()->hasRole('admin')
            ? $this->buildConnectUrl($request->user()->id)
            : null;

        return view('admin.github', compact('accounts', 'connectUrl'));
    }

    /**
     * Build a GitHub authorize URL carrying a one-time token that ties the eventual
     * callback back to the admin who initiated it, regardless of which browser completes it.
     */
    protected function buildConnectUrl(int $adminId): string
    {
        $token = Str::random(40);

        Cache::put("github-connect:{$token}", $adminId, now()->addMinutes(self::CONNECT_TOKEN_TTL_MINUTES));

        return Socialite::driver('github')
            ->stateless()
            ->scopes(self::CONNECT_SCOPES)
            ->with(['state' => $token])
            ->redirect()
            ->getTargetUrl();
    }

    /**
     * List a tracked repository's branches, each with its latest commit.
     */
    public function branches(GithubRepository $repository, Request $request): JsonResponse
    {
        $branches = $this->github->branchesWithLatestCommit($repository, fresh: $request->boolean('fresh'));

        return response()->json(['branches' => $branches]);
    }

    /**
     * Redirect to GitHub to authorize this application.
     */
    public function connect(Request $request): RedirectResponse
    {
        return redirect($this->buildConnectUrl($request->user()->id));
    }

    /**
     * Handle the callback from GitHub and store the authorized account.
     *
     * This route has no auth middleware: the connection may be completed in a browser
     * that isn't logged into the app (e.g. one signed into a different GitHub account).
     * The one-time token in `state` ties the result back to the admin who started it.
     */
    public function callback(Request $request): RedirectResponse|ViewContract
    {
        $token = $request->query('state');
        $adminId = $token ? Cache::pull("github-connect:{$token}") : null;

        if (! $adminId) {
            return $this->connectResult(false, 'This connection link has expired or was already used. Please generate a new one and try again.');
        }

        try {
            $githubUser = Socialite::driver('github')->stateless()->user();
        } catch (\Throwable $e) {
            return $this->connectResult(false, 'GitHub authorization failed. Please try again.');
        }

        GithubAccount::updateOrCreate(
            ['username' => $githubUser->getNickname()],
            [
                'label' => $githubUser->getName() ?: $githubUser->getNickname(),
                'token' => $githubUser->token,
                'avatar_url' => $githubUser->getAvatar(),
                'created_by' => $adminId,
            ]
        );

        return $this->connectResult(true, 'GitHub account connected successfully.');
    }

    /**
     * Respond to a completed authorization: redirect back into the dashboard if this
     * browser has an authenticated session, otherwise show a standalone confirmation page.
     */
    protected function connectResult(bool $success, string $message): RedirectResponse|ViewContract
    {
        if (auth()->check()) {
            return redirect()->route('admin.github.index')
                ->with($success ? 'success' : 'error', $message);
        }

        return view('admin.github-connect-result', compact('success', 'message'));
    }

    /**
     * Disconnect a GitHub account and its tracked repositories.
     */
    public function destroyAccount(GithubAccount $account): RedirectResponse
    {
        $account->delete();

        return back()->with('success', 'GitHub account removed.');
    }

    /**
     * List repositories available to an account, flagging which are already tracked.
     */
    public function repositories(GithubAccount $account): JsonResponse
    {
        $repositories = $this->github->listRepositories($account);
        $trackedFullNames = $account->repositories()->pluck('full_name')->all();

        $repositories = collect($repositories)->map(fn (array $repo) => $repo + [
            'tracked' => in_array($repo['full_name'], $trackedFullNames, true),
        ]);

        return response()->json(['repositories' => $repositories->values()]);
    }

    /**
     * Replace the set of tracked repositories for an account.
     */
    public function syncRepositories(GithubAccount $account, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'repositories' => 'present|array',
            'repositories.*.full_name' => 'required|string',
            'repositories.*.name' => 'required|string',
            'repositories.*.html_url' => 'nullable|string',
            'repositories.*.default_branch' => 'nullable|string',
        ]);

        $account->repositories()->delete();

        foreach ($validated['repositories'] as $repo) {
            $account->repositories()->create($repo);
        }

        return response()->json(['message' => 'Tracked repositories updated.']);
    }
}
