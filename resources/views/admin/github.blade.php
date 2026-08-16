@extends('layouts.dashboard')

@push('styles')
<style>
    :root {
        --tree-trunk-x: 70px; /* aligns with the center of each repo's icon */
        --tree-branch-x: 94px; /* where a branch's own icon/content begins */
    }

    .tree-gutter { width: 28px; }

    .repo-chevron { transition: transform 0.2s ease; }
    .repo-row.expanded .repo-chevron { transform: rotate(90deg); }

    .repo-branches { position: relative; }
    .tree-trunk {
        position: absolute;
        top: 0;
        left: var(--tree-trunk-x);
        width: 1px;
        background: theme('colors.gray.200');
    }
    :is(.dark) .tree-trunk { background: theme('colors.gray.700'); }

    .branch-row {
        position: relative;
        height: 2.5rem;
        animation: branchFadeIn 0.2s ease both;
    }
    .branch-row .tree-elbow {
        position: absolute;
        left: var(--tree-trunk-x);
        top: 50%;
        width: calc(var(--tree-branch-x) - var(--tree-trunk-x) - 6px);
        height: 1px;
        background: theme('colors.gray.200');
    }
    :is(.dark) .branch-row .tree-elbow { background: theme('colors.gray.700'); }
    .branch-row .tree-node {
        position: absolute;
        left: calc(var(--tree-branch-x) - 10px);
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background: theme('colors.indigo.400');
    }
    :is(.dark) .branch-row .tree-node { background: theme('colors.indigo.500'); }

    @keyframes branchFadeIn {
        from { opacity: 0; transform: translateX(-4px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>
@endpush

@section('content')
<div class="p-6 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">GitHub Commits</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Browse tracked repositories, their branches, and the latest commit on each.</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center">
            @role('admin')
                <div class="relative">
                    <button id="connectDropdownBtn" type="button"
                        class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-gray-300 dark:shadow-none">
                        <i class="fab fa-github mr-2"></i>Connect with GitHub
                        <i class="fas fa-chevron-down text-xs ml-2"></i>
                    </button>
                    <div id="connectDropdown"
                        class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50 hidden border border-gray-100 dark:border-gray-700">
                        <a href="{{ route('admin.github.connect') }}"
                            class="flex items-start gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-up-right-from-square mt-0.5 text-gray-400 dark:text-gray-500"></i>
                            <span>
                                <span class="block font-medium text-gray-900 dark:text-gray-100">Open in this browser</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Authorize the GitHub account currently signed in here</span>
                            </span>
                        </a>
                        <button type="button" onclick="copyConnectLink()"
                            class="flex items-start gap-3 w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-copy mt-0.5 text-gray-400 dark:text-gray-500"></i>
                            <span>
                                <span class="block font-medium text-gray-900 dark:text-gray-100">Copy GitHub link for another browser</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Goes straight to GitHub's authorize page &mdash; paste where you're signed in as the other account (you'll need to be signed into PMT there too)</span>
                            </span>
                        </button>
                    </div>
                </div>
            @endrole
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-md p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-indigo-100 text-sm font-medium">Connected Accounts</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fab fa-github text-white"></i>
                </div>
            </div>
            <span class="text-3xl font-bold">{{ $accounts->count() }}</span>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-md p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-blue-100 text-sm font-medium">Tracked Repositories</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-book text-white"></i>
                </div>
            </div>
            <span class="text-3xl font-bold">{{ $accounts->sum(fn ($account) => $account->repositories->count()) }}</span>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-md p-5 text-white sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-3">
                <span class="text-emerald-100 text-sm font-medium">Auto-Refreshed Cache</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-rotate text-white"></i>
                </div>
            </div>
            <span class="text-lg font-semibold">Every 10 minutes</span>
        </div>
    </div>

    @role('admin')
        <!-- Connected Accounts -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Connected Accounts</h3>

            @if($accounts->isEmpty())
                <div class="text-center py-8">
                    <i class="fab fa-github text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No GitHub accounts connected yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="accountsList">
                    @foreach($accounts as $account)
                        @php $repoCount = $account->repositories->count(); @endphp
                        <div class="group relative rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/60 p-4 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $account->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($account->username) }}"
                                        alt="{{ $account->username }}" class="w-12 h-12 rounded-full ring-2 ring-white dark:ring-gray-900 shadow-sm">
                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-gray-900 dark:bg-gray-950 ring-2 ring-white dark:ring-gray-800/60 flex items-center justify-center">
                                        <i class="fab fa-github text-white text-[10px]"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" title="{{ $account->label }}">{{ $account->label }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">&#64;{{ $account->username }}</p>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 flex-shrink-0">
                                    <i class="fas fa-book text-[10px]"></i>
                                    {{ $repoCount }} repo{{ $repoCount === 1 ? '' : 's' }}
                                </span>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button type="button" onclick="openRepoModal({{ $account->id }}, '{{ addslashes($account->label) }}')"
                                        title="Manage repositories"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <i class="fas fa-list-check text-sm"></i>
                                    </button>
                                    <form action="{{ route('admin.github.accounts.destroy', $account) }}" method="POST"
                                        onsubmit="return confirm('Remove {{ addslashes($account->label) }}? Tracked repositories will stop appearing here.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Remove account"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endrole

    <!-- Repositories & Branches -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Repositories</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Expand a repository to see its branches and their latest commit</p>
            </div>
            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="repoSearchFilter" placeholder="Filter repositories..."
                    class="w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div id="repoTree" class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($accounts as $account)
                @foreach($account->repositories as $repo)
                    <div class="repo-row" data-repo-id="{{ $repo->id }}" data-search="{{ strtolower($repo->full_name) }}">
                        <button type="button" class="repo-toggle w-full flex items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors text-left">
                            <div class="flex items-center min-w-0">
                                <span class="tree-gutter flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-chevron-right repo-chevron text-gray-400 dark:text-gray-500 text-xs"></i>
                                </span>
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-indigo-50 dark:bg-indigo-500/10 flex-shrink-0 mr-3">
                                    <i class="fas fa-book text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $repo->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        <img src="{{ $account->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($account->username) }}" class="inline w-3.5 h-3.5 rounded-full align-[-2px] mr-1">
                                        {{ $account->label }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-code-branch mr-1 text-[10px]"></i>{{ $repo->default_branch ?? 'default' }}
                                </span>
                                <span class="branch-count text-xs text-gray-400 dark:text-gray-500 w-20 text-right"></span>
                            </div>
                        </button>
                        <div class="repo-branches hidden" data-loaded="0"></div>
                    </div>
                @endforeach
            @empty
            @endforelse
        </div>

        <div id="repoTreeEmpty" class="{{ $accounts->sum(fn ($account) => $account->repositories->count()) > 0 ? 'hidden' : '' }} px-6 py-10 text-center">
            <i class="fas fa-code-branch text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">No repositories tracked yet.</p>
            @role('admin')
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Connect an account above, then choose repositories to track.</p>
            @endrole
        </div>

        <div id="repoSearchEmpty" class="hidden px-6 py-10 text-center">
            <i class="fas fa-search text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">No repositories match your filter.</p>
        </div>
    </div>

    @role('admin')
        <!-- Repository Picker Modal -->
        <div id="repoModal" class="fixed inset-0 z-50 hidden" aria-labelledby="repo-modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRepoModal()"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform rounded-xl bg-white dark:bg-gray-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 sm:px-6 rounded-t-xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold leading-6 text-white" id="repo-modal-title">
                                    <i class="fas fa-list-check mr-2"></i>Repositories &mdash; <span id="repoModalAccountLabel"></span>
                                </h3>
                                <button type="button" onclick="closeRepoModal()" class="text-white hover:text-gray-200 transition-colors">
                                    <i class="fas fa-times text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="px-4 py-4 sm:px-6">
                            <input type="text" id="repoSearchInput" placeholder="Search repositories..."
                                class="w-full mb-3 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                            <div id="repoModalList" class="max-h-80 overflow-y-auto space-y-1 text-left">
                                <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Loading repositories...</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-xl">
                            <button type="button" onclick="saveRepoSelection()"
                                class="inline-flex w-full justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-purple-700 sm:ml-3 sm:w-auto transition-all">
                                <i class="fas fa-save mr-2"></i>Save
                            </button>
                            <button type="button" onclick="closeRepoModal()"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto transition-all">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endrole
</div>
@endsection

@push('scripts')
<script>
    const ROUTES = {
        branches: @js(route('admin.github.branches', ['repository' => '__ID__'])),
        repositories: @js(route('admin.github.repositories', ['account' => '__ID__'])),
        repositoriesSync: @js(route('admin.github.repositories.sync', ['account' => '__ID__'])),
    };

    let currentRepoAccountId = null;
    let currentRepoData = [];

    function timeAgo(dateString) {
        const seconds = Math.floor((Date.now() - new Date(dateString)) / 1000);
        const units = [['year', 31536000], ['month', 2592000], ['day', 86400], ['hour', 3600], ['minute', 60]];
        for (const [name, secondsInUnit] of units) {
            const value = Math.floor(seconds / secondsInUnit);
            if (value >= 1) {
                return `${value} ${name}${value > 1 ? 's' : ''} ago`;
            }
        }
        return 'just now';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function branchRowHtml(branch) {
        const commit = branch.commit;
        const connector = '<span class="tree-elbow"></span><span class="tree-node"></span>';
        const content = 'padding-left: var(--tree-branch-x); padding-right: 1.5rem;';

        if (!commit) {
            return `
                <div class="branch-row flex items-center gap-3" style="${content}">
                    ${connector}
                    <i class="fas fa-code-branch text-gray-300 dark:text-gray-600 text-xs flex-shrink-0"></i>
                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400 flex-shrink-0">${escapeHtml(branch.name)}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">No commit data available</span>
                </div>
            `;
        }

        return `
            <a href="${commit.html_url}" target="_blank" rel="noopener noreferrer"
                class="branch-row flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors group" style="${content}">
                ${connector}
                <i class="fas fa-code-branch text-gray-400 dark:text-gray-500 text-xs flex-shrink-0"></i>
                <span class="text-xs font-mono text-gray-700 dark:text-gray-300 flex-shrink-0">${escapeHtml(branch.name)}</span>
                ${branch.is_default ? '<span class="flex-shrink-0 px-1.5 py-0.5 text-[10px] font-semibold rounded bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400">default</span>' : ''}
                <img src="${commit.author_avatar ?? 'https://ui-avatars.com/api/?name=' + encodeURIComponent(commit.author_name ?? '?')}"
                    alt="${escapeHtml(commit.author_name)}" class="w-5 h-5 rounded-full flex-shrink-0">
                <span class="text-sm text-gray-600 dark:text-gray-300 truncate flex-1 group-hover:text-gray-900 dark:group-hover:text-gray-100">${escapeHtml(commit.message.split('\n')[0])}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">${commit.committed_at ? timeAgo(commit.committed_at) : ''}</span>
                <code class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">${commit.sha.substring(0, 7)}</code>
            </a>
        `;
    }

    const BRANCH_ROW_HEIGHT = 40; // px, must match .branch-row { height: 2.5rem }

    function withTrunk(rowsHtml, rowCount) {
        const trunkHeight = Math.max(rowCount - 1, 0) * BRANCH_ROW_HEIGHT + (BRANCH_ROW_HEIGHT / 2);

        return `
            <div>
                <span class="tree-trunk" style="height: ${trunkHeight}px"></span>
                ${rowsHtml}
            </div>
        `;
    }

    function skeletonHtml() {
        const rows = [1, 2, 3].map(() => `
            <div class="branch-row flex items-center gap-3 animate-pulse" style="padding-left: var(--tree-branch-x); padding-right: 1.5rem;">
                <span class="tree-elbow"></span><span class="tree-node"></span>
                <div class="w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                <div class="h-3 w-1/3 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
        `).join('');

        return withTrunk(rows, 3);
    }

    function loadBranches(repoRow, fresh = false) {
        const repoId = repoRow.dataset.repoId;
        const branchesEl = repoRow.querySelector('.repo-branches');
        const countEl = repoRow.querySelector('.branch-count');

        branchesEl.innerHTML = skeletonHtml();
        branchesEl.classList.remove('hidden');

        axios.get(ROUTES.branches.replace('__ID__', repoId), { params: fresh ? { fresh: 1 } : {} })
            .then(response => {
                const branches = response.data.branches;
                branchesEl.dataset.loaded = '1';
                countEl.textContent = `${branches.length} branch${branches.length === 1 ? '' : 'es'}`;

                if (!branches.length) {
                    branchesEl.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500 py-3" style="padding-left: var(--tree-branch-x);">No branches found.</p>';
                    return;
                }

                branchesEl.innerHTML = withTrunk(branches.map(branchRowHtml).join(''), branches.length);
            })
            .catch(() => {
                branchesEl.innerHTML = '<p class="text-xs text-red-500 py-3" style="padding-left: var(--tree-branch-x);">Could not load branches. The token may be invalid.</p>';
                branchesEl.dataset.loaded = '0';
            });
    }

    document.querySelectorAll('.repo-toggle').forEach(button => {
        button.addEventListener('click', function () {
            const repoRow = this.closest('.repo-row');
            const branchesEl = repoRow.querySelector('.repo-branches');
            const isOpen = repoRow.classList.toggle('expanded');

            branchesEl.classList.toggle('hidden', !isOpen);

            if (isOpen && branchesEl.dataset.loaded !== '1') {
                loadBranches(repoRow);
            }
        });
    });

    document.getElementById('repoSearchFilter').addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.repo-row').forEach(row => {
            const matches = row.dataset.search.includes(query);
            row.classList.toggle('hidden', !matches);
            if (matches) visibleCount++;
        });

        document.getElementById('repoSearchEmpty').classList.toggle('hidden', visibleCount !== 0 || query === '');
    });

    @role('admin')
        const connectDropdownBtn = document.getElementById('connectDropdownBtn');
        const connectDropdown = document.getElementById('connectDropdown');

        connectDropdownBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            connectDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!connectDropdown.contains(e.target) && !connectDropdownBtn.contains(e.target)) {
                connectDropdown.classList.add('hidden');
            }
        });

        function copyConnectLink() {
            connectDropdown.classList.add('hidden');

            navigator.clipboard.writeText(@json($connectUrl))
                .then(() => SwalDefault.fire({ icon: 'success', title: 'Link copied', text: 'Paste it into the browser signed into the other GitHub account.', timer: 2500, showConfirmButton: false }))
                .catch(() => SwalDefault.fire({ icon: 'error', title: 'Could not copy link' }));
        }

        function openRepoModal(accountId, label) {
            currentRepoAccountId = accountId;
            document.getElementById('repoModalAccountLabel').textContent = label;
            document.getElementById('repoModalList').innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Loading repositories...</p>';
            document.getElementById('repoModal').classList.remove('hidden');

            axios.get(ROUTES.repositories.replace('__ID__', accountId))
                .then(response => {
                    currentRepoData = response.data.repositories;
                    renderRepoList(currentRepoData);
                })
                .catch(() => {
                    document.getElementById('repoModalList').innerHTML = '<p class="text-sm text-red-500 py-4 text-center">Could not load repositories. The token may be invalid.</p>';
                });
        }

        function closeRepoModal() {
            document.getElementById('repoModal').classList.add('hidden');
            currentRepoAccountId = null;
            currentRepoData = [];
        }

        function renderRepoList(repos) {
            const list = document.getElementById('repoModalList');
            if (!repos.length) {
                list.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">No repositories found.</p>';
                return;
            }
            list.innerHTML = repos.map(repo => `
                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                    <input type="checkbox" class="repo-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        value="${escapeHtml(repo.full_name)}" ${repo.tracked ? 'checked' : ''}>
                    <span class="text-sm text-gray-800 dark:text-gray-200">${escapeHtml(repo.full_name)}</span>
                </label>
            `).join('');
        }

        document.getElementById('repoSearchInput').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            renderRepoList(currentRepoData.filter(r => r.full_name.toLowerCase().includes(query)));
        });

        function saveRepoSelection() {
            const selected = Array.from(document.querySelectorAll('.repo-checkbox:checked')).map(cb => {
                const repo = currentRepoData.find(r => r.full_name === cb.value);
                return {
                    full_name: repo.full_name,
                    name: repo.name,
                    html_url: repo.html_url,
                    default_branch: repo.default_branch,
                };
            });

            axios.put(ROUTES.repositoriesSync.replace('__ID__', currentRepoAccountId), { repositories: selected })
                .then(() => window.location.reload())
                .catch(() => SwalDefault.fire({ icon: 'error', title: 'Could not update repositories' }));
        }
    @endrole
</script>
@endpush
