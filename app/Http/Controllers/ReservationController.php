<?php

namespace App\Http\Controllers;

use App\Filters\ReservationFilter;
use App\Http\Resources\Reservation\ReservationDetails;
use App\Http\Resources\Reservation\ReservationRequestDetails;
use App\Http\Services\ReservationService;

class ReservationController extends Controller
{
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->middleware(middleware: ['auth:sanctum', 'isApproved', 'setLocale']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ReservationFilter $filter)
    {
        $reservations = $this->reservationService->getAll($filter);

        return ReservationDetails::query($reservations);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $resrvation = $this->reservationService->find($id);

        return new ReservationDetails($resrvation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function accept($reservation_request_id)
    {
        $reservation = $this->reservationService->accept($reservation_request_id);

        return response()->json([
            'message' => __('notifications.reservation_accepted_body'),
            'data' => new ReservationDetails($reservation),
        ]);
    }

    public function reject($reservation_request_id)
    {
        $reservation_request = $this->reservationService->reject($reservation_request_id);

        return response()->json([
            'message' => __('notifications.reservation_rejected_body'),
            'data' => new ReservationRequestDetails($reservation_request),
        ]);
    }
}
