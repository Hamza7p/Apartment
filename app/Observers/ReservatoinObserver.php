<?php

namespace App\Observers;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservatoinObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        $this->updateApartmentAvailability($reservation);
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
        $this->updateApartmentAvailability($reservation);
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $reservation): void
    {
        $this->updateApartmentAvailability($reservation);

    }

    /**
     * Handle the Reservation "restored" event.
     */
    public function restored(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "force deleted" event.
     */
    public function forceDeleted(Reservation $reservation): void
    {
        //
    }

    protected function updateApartmentAvailability(Reservation $reservation)
    {
        $apartment = $reservation->apartment;

        if (! $apartment) {
            return;
        }

        $activeReservation = $apartment->reservations()
            ->where('status', ReservationStatus::ACTIVE->value)
            ->orderByDesc('end_date')
            ->first();

        // if the apartment is reserves now update the apartment availability to the end date of reservation
        if ($activeReservation) {
            $apartment->available_at = Carbon::parse($activeReservation->end_date)->addDay();
        } else { // if the apartment is not reserved now then update the availability to now
            $apartment->available_at = now()->addDay();
        }

        $apartment->saveQuietly();
    }
}
