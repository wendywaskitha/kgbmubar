<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\TenantContextMiddleware::class);
    })
    ->withCommands()
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        // Schedule the KGB reminder command to run daily
        $schedule->command('notifications:send-kgb-reminders')->daily();

        // Schedule the report generation command
        $schedule->command('reports:generate-scheduled --type=monthly --send-email')->monthlyOn(1, '01:00');
        $schedule->command('reports:generate-scheduled --type=weekly --send-email')->weeklyOn(1, '01:00'); // Every Monday
        $schedule->command('reports:generate-scheduled --type=daily --send-email')->dailyAt('01:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
