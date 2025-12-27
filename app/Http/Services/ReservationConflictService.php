<?php

namespace App\Http\Services;

use App\Http\Services\Base\CrudService;
use App\Models\Reservation;

class ReservationConflictService // extends CrudService
{
    /**
     * Check if a reservation conflicts with existing reservations
     */
    public function hasConflict(
        int $apartmentId,
        string $startDate,
        string $endDate
    ): bool {
        return Reservation::where('apartment_id', $apartmentId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->exists();
    }
}
