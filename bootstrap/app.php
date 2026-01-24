<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Register custom error views
    })
    ->withProviders([
        // Package Service Providers
        \Spatie\Permission\PermissionServiceProvider::class,
    ])
    ->withSchedule(function (Schedule $schedule): void {
        // Send due task notifications daily at 9 AM
        $schedule->command('app:send-due-task-notifications')
            ->dailyAt('09:00')
            ->description('Send notifications for tasks due within 3 days');
    })
    ->create();
