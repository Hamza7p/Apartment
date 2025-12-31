<?php

namespace App\Http\Services;

use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Reservation;
use App\Models\ReservationRequest;

class ReservationConflictService // extends CrudService
{
    /**
     * Check if a reservation conflicts with existing reservations
     */
    public function hasConflict(
        int $apartmentId,
        string $startDate,
        string $endDate,
        ?int $exceptReservationId = null
    ): bool {

        return Reservation::where('apartment_id', $apartmentId)
            ->where('status', ReservationStatus::ACTIVE->value)
            ->when($exceptReservationId, function ($q) use ($exceptReservationId) {
                $q->where('id', '!=', $exceptReservationId);
            })
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<', $endDate)
                    ->where('end_date', '>', $startDate);
            })
            ->exists();

    }

    /**
     * Reject all reservation requests that conflict with a given reservation request
     *
     * @return int Number of reservations rejected
     */
    public function rejectConflictingRequests(
        int $apartmentId,
        string $startDate,
        string $endDate,
        ?int $exceptReservationId = null
    ): int {
        return ReservationRequest::where('apartment_id', $apartmentId)
            ->where('status', ReservationStatus::PENDING->value)
            ->when($exceptReservationId, function ($q) use ($exceptReservationId) {
                $q->where('id', '!=', $exceptReservationId);
            })
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->update(['status' => ReservationStatus::REJECTED->value]);
    }
}
