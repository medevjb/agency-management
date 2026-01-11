<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FreelanceProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TechStackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/dashboard', [OrderDashboardController::class, 'index'])->name('orders.dashboard');
    Route::get('/orders/dashboard/chart-data', [OrderDashboardController::class, 'getChartData'])->name('orders.dashboard.chart-data');
    Route::get('/orders-data', [OrderController::class, 'getOrders'])->name('orders.data');
    Route::post('/orders/bulk-action', [OrderController::class, 'bulkAction'])->name('orders.bulk-action');
    Route::get('/orders/search-members', [OrderController::class, 'searchMembers'])->name('orders.search-members');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/quick-update', [OrderController::class, 'quickUpdate'])->name('orders.quick-update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Tasks routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks-data', [TaskController::class, 'getTasks'])->name('tasks.data');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Bug Reports routes
    Route::prefix('bug-reports')->name('bug-reports.')->group(function () {
        Route::get('/', [BugReportController::class, 'index'])->name('index');
        Route::get('/create', [BugReportController::class, 'create'])->name('create');
        Route::post('/', [BugReportController::class, 'store'])->name('store');
        Route::get('/{bugReport}', [BugReportController::class, 'show'])->name('show');
        Route::get('/{bugReport}/edit', [BugReportController::class, 'edit'])->name('edit');
        Route::patch('/{bugReport}', [BugReportController::class, 'update'])->name('update');
        Route::delete('/{bugReport}', [BugReportController::class, 'destroy'])->name('destroy');
        Route::delete('/{bugReport}/images/{image}', [BugReportController::class, 'deleteImage'])->name('images.delete');
    });

    // Teams routes
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams-data', [TeamController::class, 'getTeams'])->name('teams.data');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::patch('/teams/{id}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::post('/teams/{id}/add-members', [TeamController::class, 'addMembers'])->name('teams.add-members');
    Route::delete('/teams/{id}/members/{user_id}', [TeamController::class, 'removeMember'])->name('teams.remove-member');

    // Tech Stacks routes
    Route::get('/tech-stacks', [TechStackController::class, 'index'])->name('tech-stacks.index');
    Route::get('/tech-stacks-data', [TechStackController::class, 'getTechStacks'])->name('tech-stacks.data');
    Route::get('/tech-stacks/create', [TechStackController::class, 'create'])->name('tech-stacks.create');
    Route::post('/tech-stacks', [TechStackController::class, 'store'])->name('tech-stacks.store');
    Route::get('/tech-stacks/{id}', [TechStackController::class, 'show'])->name('tech-stacks.show');
    Route::get('/tech-stacks/{id}/edit', [TechStackController::class, 'edit'])->name('tech-stacks.edit');
    Route::patch('/tech-stacks/{id}', [TechStackController::class, 'update'])->name('tech-stacks.update');
    Route::delete('/tech-stacks/{id}', [TechStackController::class, 'destroy'])->name('tech-stacks.destroy');
    Route::post('/tech-stacks/{id}/add-members', [TechStackController::class, 'addMembers'])->name('tech-stacks.add-members');
    Route::delete('/tech-stacks/{id}/members/{user_id}', [TechStackController::class, 'removeMember'])->name('tech-stacks.remove-member');

    // Portfolios routes
    Route::prefix('portfolios')->name('portfolios.')->group(function () {
        Route::get('/', [PortfolioController::class, 'index'])->name('index');
        Route::get('/my', [PortfolioController::class, 'myPortfolios'])->name('my');
        Route::get('/create', [PortfolioController::class, 'create'])->name('create');
        Route::post('/', [PortfolioController::class, 'store'])->name('store');
        Route::get('/{portfolio}', [PortfolioController::class, 'show'])->name('show');
        Route::get('/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('edit');
        Route::patch('/{portfolio}', [PortfolioController::class, 'update'])->name('update');
        Route::delete('/{portfolio}', [PortfolioController::class, 'destroy'])->name('destroy');
        Route::patch('/{portfolio}/toggle-status', [PortfolioController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{portfolio}/toggle-featured', [PortfolioController::class, 'toggleFeatured'])->name('toggle-featured');
    });

    // Freelance Profiles Routes
    Route::prefix('freelance-profiles')->name('freelance-profiles.')->group(function () {
        Route::get('/', [FreelanceProfileController::class, 'index'])->name('index');
        Route::get('/create', [FreelanceProfileController::class, 'create'])->name('create');
        Route::post('/', [FreelanceProfileController::class, 'store'])->name('store');
        Route::get('/{profile}', [FreelanceProfileController::class, 'show'])->name('show');
        Route::get('/{profile}/edit', [FreelanceProfileController::class, 'edit'])->name('edit');
        Route::patch('/{profile}', [FreelanceProfileController::class, 'update'])->name('update');
        Route::patch('/{profile}/toggle-status', [FreelanceProfileController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{profile}', [FreelanceProfileController::class, 'destroy'])->name('destroy');
    });

    // Users Management Routes - Only for users with admin role
    Route::middleware(['auth', 'role:admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'getUsers'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::get('/stats', [UserController::class, 'getStats'])->name('stats');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('/bulk-import', [UserController::class, 'bulkImportForm'])->name('bulk-import.form');
        Route::post('/bulk-import', [UserController::class, 'bulkImport'])->name('bulk-import.store');
        Route::post('/bulk-operation', [UserController::class, 'bulkOperation'])->name('bulk-operation');
    });
});

// Admin Routes - Only for users with admin role
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
    Route::get('/roles/create', [AdminController::class, 'createRole'])->name('roles.create');
    Route::post('/roles', [AdminController::class, 'storeRole'])->name('roles.store');
    Route::get('/roles/{roleName}/edit', [AdminController::class, 'editRole'])->name('roles.edit');
    Route::patch('/roles/{roleName}', [AdminController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{roleName}', [AdminController::class, 'deleteRole'])->name('roles.delete');
    Route::post('/roles/{roleName}/users/{user}', [AdminController::class, 'assignUserToRole'])->name('roles.assignUser');
    Route::delete('/roles/{roleName}/users/{user}', [AdminController::class, 'removeUserFromRole'])->name('roles.removeUser');
    Route::get('/users/{user}/roles', [AdminController::class, 'editUserRoles'])->name('users.roles');
    Route::patch('/users/{user}/roles', [AdminController::class, 'updateUserRoles'])->name('users.roles.update');
});

// Settings Routes - Only for users with admin role
Route::middleware(['auth', 'role:admin'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/{group}', [SettingsController::class, 'index'])->name('group');
    Route::put('/{group}', [SettingsController::class, 'update'])->name('update');
    Route::post('/delete-value/{key}', [SettingsController::class, 'deleteValue'])->name('delete-value');

    // Project Status Management
    Route::post('/project-statuses', [SettingsController::class, 'addProjectStatus'])->name('project-statuses.store');
    Route::put('/project-statuses/{key}', [SettingsController::class, 'updateProjectStatus'])->name('project-statuses.update');
    Route::delete('/project-statuses/{key}', [SettingsController::class, 'deleteProjectStatus'])->name('project-statuses.destroy');
});

require __DIR__.'/auth.php';
