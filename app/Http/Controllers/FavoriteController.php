<?php

namespace App\Http\Controllers;

use App\Http\Resources\Apartment\ApartmentDetails;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'isApproved', 'setLocale']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favoriteApartments = Auth::user()->favoriteApartments()->with('photos')->get();

        return ApartmentDetails::collection($favoriteApartments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $apartment_id)
    {
        Auth::user()->favoriteApartments()->syncWithoutDetaching($apartment_id);

        return response()->json(['success' => true], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $apartment_id)
    {
        Auth::user()->favoriteApartments()->detach($apartment_id);

        return response()->noContent();
    }
}
