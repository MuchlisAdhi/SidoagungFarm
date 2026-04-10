<?php

namespace App\Providers;

use App\Services\Contracts\INavigationService;
use App\Services\NavigationService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        RateLimiter::for('notification-emails-global', function (): Limit {
            return Limit::perHour(1)->by('notification-emails-global');
        });

        $appUrl = (string) config('app.url');
        if ($appUrl !== '') {
            URL::forceRootUrl(rtrim($appUrl, '/'));

            if (str_starts_with(strtolower($appUrl), 'https://')) {
                URL::forceScheme('https');
            }
        }

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
