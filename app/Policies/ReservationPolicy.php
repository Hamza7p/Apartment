<?php

namespace App\Policies;

use App\Models\reservation;
use App\Models\User;

class ReservationPolicy
{
    public function modify(User $user, reservation $reservation): bool
    {
        return $user->id === $reservation->user_id || $user->id === $reservation->apartment->user_id;
    }

    public function modifyDates(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }

    public function modifyPrice(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->apartment->user_id;
    }

    public function respondToDateModification(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->apartment->user_id;
    }

    public function respondToPriceModification(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }

    public function respondToCancel(User $user, Reservation $reservation): bool
    {
        return in_array($user->id, [
            $reservation->user_id,
            $reservation->apartment->user_id,
        ]);
    }
}
