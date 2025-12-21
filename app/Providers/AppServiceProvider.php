<?php

namespace App\Providers;

use App\Filters\Base\FilterDataProvider;
use App\Filters\Base\HttpFilterDataAdapter;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilterDataProvider::class,HttpFilterDataAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
