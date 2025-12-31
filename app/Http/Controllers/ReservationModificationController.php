<?php

namespace App\Http\Controllers;

use App\Filters\ReservationModificationFilter;
use App\Http\Requests\ReservationModificationRequest;
use App\Http\Resources\Reservation\ReservationModificationDetails;
use App\Http\Services\ReservationModificationService;

class ReservationModificationController extends Controller
{
    protected ReservationModificationService $reservationModificationService;

    public function __construct(ReservationModificationService $reservationModificationService)
    {
        $this->reservationModificationService = $reservationModificationService;
        $this->middleware(middleware: ['auth:sanctum', 'isApproved', 'setLocale']);

    }

    /**
     * Display a listing of the resource.
     */
    public function index(ReservationModificationFilter $filter)
    {
        $reservations = $this->reservationModificationService->getAll($filter);

        return ReservationModificationDetails::query($reservations);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $resrvation_request = $this->reservationModificationService->find($id);

        return new ReservationModificationDetails($resrvation_request);
    }

    public function requestModification(ReservationModificationRequest $request, int $id)
    {
        $modification = $this->reservationModificationService->requestModification($id, $request->validated());

        return new ReservationModificationDetails($modification);
    }

    public function acceptModification(int $modificationId)
    {
        $modification = $this->reservationModificationService->acceptModification($modificationId);

        return new ReservationModificationDetails($modification);

    }

    public function rejectModification(int $modificationId)
    {
        $modification = $this->reservationModificationService->rejectModification($modificationId);

        return new ReservationModificationDetails($modification);

    }
}
