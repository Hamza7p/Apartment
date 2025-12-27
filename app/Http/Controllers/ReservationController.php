<?php

namespace App\Http\Controllers;

use App\Http\Resources\Reservation\ReservationDetails;
use App\Http\Services\ReservationService;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->middleware(['auth:sanctum', 'isApproved', 'setLocale']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function accept($reservation_request_id)
    {
        // dd($request->all());
        $reservation = $this->reservationService->accept($reservation_request_id);

        return response()->json([
            'message' => __('notifications.reservation_accepted_body'),
            'data' => new ReservationDetails($reservation),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
