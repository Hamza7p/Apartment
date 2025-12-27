<?php

namespace App\Http\Controllers;

use App\Filters\ReservationRequestFilter;
use App\Http\Requests\ReservationRequest as RequestsReservationRequest;
use App\Http\Resources\Reservation\ReservationRequestDetails;
use App\Http\Services\ReservationRequestService;
use App\Models\ReservationRequest;
use Illuminate\Http\Request;

class ReservationRequestController extends Controller
{
    private ReservationRequestService $reservationRequestService;

    private ReservationRequestFilter $reservationRequestFilter;

    public function __construct(
        ReservationRequestService $reservationRequestService,
        ReservationRequestFilter $reservationRequestFilter
    ) {
        $this->reservationRequestService = $reservationRequestService;
        $this->reservationRequestFilter = $reservationRequestFilter;
        $this->middleware(['auth:sanctum', 'isApproved', 'setLocale']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ReservationRequestFilter $filter)
    {
        $reservationRequests = $this->reservationRequestService->getAll($filter);

        return ReservationRequestDetails::query($reservationRequests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsReservationRequest $request)
    {
        $query = $this->reservationRequestService->create($request->validated());

        return new ReservationRequestDetails($query);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReservationRequest $reservationRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReservationRequest $reservationRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReservationRequest $reservationRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReservationRequest $reservationRequest)
    {
        //
    }
}
