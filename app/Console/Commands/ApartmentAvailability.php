<?php

namespace App\Console\Commands;

use App\Models\Apartment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApartmentAvailability extends Command
{
    protected $signature = 'apartment:availability';

    protected $description = 'Fix apartment availability start dates';

    public function handle()
    {
        $today = Carbon::today();
        $updated = 0;

        Apartment::whereNotNull('availability')
            ->chunk(100, function ($apartments) use ($today, &$updated) {

                foreach ($apartments as $apartment) {
                    $availability = $apartment->availability;

                    if (! is_array($availability)) {
                        continue;
                    }

                    $changed = false;

                    $newAvailability = [];

                    foreach ($availability as $period) {
                        if (! isset($period['from'], $period['to'])) {
                            continue;
                        }

                        $from = Carbon::parse($period['from']);
                        $to = Carbon::parse($period['to']);

                        // ❌ Invalid or expired period → remove
                        if ($from->gte($to) || $to->lte($today)) {
                            continue;
                        }

                        // 🔄 Adjust start if in the past
                        if ($from->lt($today)) {
                            $from = $today->copy();
                        }

                        $newAvailability[] = [
                            'from' => $from->toDateTimeString(),
                            'to' => $to->toDateTimeString(),
                        ];
                    }

                    if ($newAvailability !== $availability) {
                        $apartment->availability = $newAvailability;
                        $apartment->save();
                    }

                }
            });

        $this->info("Updated {$updated} apartments availability.");
    }
}
