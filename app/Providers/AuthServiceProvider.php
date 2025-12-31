<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\System;
use App\Policies\ReservationPolicy;
use App\Policies\SystemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        System::class => SystemPolicy::class,
        Reservation::class => ReservationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::policy(System::class, SystemPolicy::class);
        Gate::policy(Reservation::class, ReservationPolicy::class);
    }
}
