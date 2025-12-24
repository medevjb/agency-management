<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TechStackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects-data', [ProjectController::class, 'getProjects'])->name('projects.data');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

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
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
    Route::get('/roles/{roleName}/edit', [AdminController::class, 'editRole'])->name('roles.edit');
    Route::patch('/roles/{roleName}', [AdminController::class, 'updateRole'])->name('roles.update');
});

// Settings Routes - Only for users with admin role
Route::middleware(['auth', 'role:admin'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/{group}', [SettingsController::class, 'index'])->name('group');
    Route::put('/{group}', [SettingsController::class, 'update'])->name('update');
    Route::post('/delete-value/{key}', [SettingsController::class, 'deleteValue'])->name('delete-value');
});

require __DIR__.'/auth.php';
