<?php

namespace App\Providers;

use App\Filters\Base\FilterDataProvider;
use App\Filters\Base\HttpFilterDataAdapter;
<<<<<<< HEAD
use App\Models\Review;
use App\Observers\ReviewOpserver;
=======
use App\Models\User;
use App\Observers\UserObserver;
>>>>>>> 9bf36a3d57ddf14682f5209546a90751d3fcd124
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilterDataProvider::class, HttpFilterDataAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
        Review::observe(ReviewOpserver::class);
=======
        User::observe(UserObserver::class);
>>>>>>> 9bf36a3d57ddf14682f5209546a90751d3fcd124
    }
}
