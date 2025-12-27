<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\System;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class SystemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function getData()
    {
        Gate::authorize('view', System::class);

        return response()->json([
            'users_count' => User::count(),
            'apartments_count' => Apartment::count(),
            'reservations_count' => Reservation::count(),
        ]);
    }
}
