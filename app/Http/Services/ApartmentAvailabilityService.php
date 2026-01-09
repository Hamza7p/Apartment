<?php

namespace App\Http\Services;

use App\Models\Apartment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ApartmentAvailabilityService
{
    /**
     * Remove a reserved period from apartment availability
     */
    public function applyReservation(
        Apartment $apartment,
        Carbon $start,
        Carbon $end
    ): array {
        $availability = $apartment->availability;

        // If availability is null or invalid → set full-time availability
        if (! is_array($availability) || empty($availability)) {
            $availability = [
                [
                    'from' => now()->toDateTimeString(),
                    'to' => '2099-12-31',
                ],
            ];
        }

        $newAvailability = [];

        foreach ($availability as $period) {
            $periodStart = Carbon::parse($period['from']);
            $periodEnd = Carbon::parse($period['to']);

            // No overlap → keep period
            if ($end->lt($periodStart) || $start->gt($periodEnd)) {
                $newAvailability[] = $period;

                continue;
            }

            // Before reservation
            if ($periodStart->lt($start)) {
                $newAvailability[] = [
                    'from' => $periodStart->toDateTimeString(),
                    'to' => $start->copy()->subDay()->toDateTimeString(),
                ];
            }

            // After reservation
            if ($periodEnd->gt($end)) {
                $newAvailability[] = [
                    'from' => $end->copy()->addDay()->toDateTimeString(),
                    'to' => $periodEnd->toDateTimeString(),
                ];
            }
        }

        return $newAvailability;
    }

    public function isAvailableNow(Apartment $apartment, Carbon $time): bool
    {
        $availability = $apartment->availability;

        // No availability defined → available by default
        if (! is_array($availability) || empty($availability)) {
            return true;
        }

        foreach ($availability as $period) {
            if (
                isset($period['from'], $period['to']) &&
                $time->between(
                    Carbon::parse($period['from']),
                    Carbon::parse($period['to'])
                )
            ) {
                return true;
            }
        }

        return false;
    }

    public function getAvailableNow(): Collection
    {
        $now = now();

        return Apartment::query()->get()
            ->filter(function (Apartment $apartment) use ($now) {
                return $this->isAvailableNow($apartment, $now);
            })
            ->values();
    }
}
