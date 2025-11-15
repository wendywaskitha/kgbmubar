<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SystemSettingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind SystemSettingService as a singleton
        $this->app->singleton(SystemSettingService::class, function ($app) {
            return new SystemSettingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
