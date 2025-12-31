<?php

namespace App\Providers;

use App\Filters\Base\FilterDataProvider;
use App\Filters\Base\HttpFilterDataAdapter;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Channels\DatabaseChannel;
use App\Notifications\Channels\FcmChannel;
use App\Observers\ReviewObserver;
use App\Observers\UserObserver;
use Illuminate\Notifications\ChannelManager;
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
        Review::observe(ReviewObserver::class);
        User::observe(UserObserver::class);

        $this->app->make(ChannelManager::class)->extend('fcm', fn ($app) => new FcmChannel);
        $this->app->make(ChannelManager::class)->extend('database', fn ($app) => new DatabaseChannel);

    }
}
