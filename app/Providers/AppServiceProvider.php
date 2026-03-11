<?php

namespace App\Providers;

use App\Services\Contracts\INavigationService;
use App\Services\NavigationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(INavigationService::class, NavigationService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        try {
            app(INavigationService::class)->BootstrapNavigationAccess();
        } catch (\Throwable $th) {
            report($th);
        }
    }
}
