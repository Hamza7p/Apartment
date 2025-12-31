<?php

namespace App\Http\Controllers;

use App\Filters\ReservationRequestFilter;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\Reservation\ReservationRequestDetails;
use App\Http\Services\ReservationRequestService;

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
    public function store(ReservationRequest $request)
    {
        $query = $this->reservationRequestService->create($request->validated());

        return new ReservationRequestDetails($query);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $resrvation_request = $this->reservationRequestService->find($id);

        return new ReservationRequestDetails($resrvation_request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ReservationRequest $request)
    {
        $resrvation_request = $this->reservationRequestService->update($id, $request->validated());

        return new ReservationRequestDetails($resrvation_request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->reservationRequestService->cancel($id);

        return response()->noContent();
    }
}
