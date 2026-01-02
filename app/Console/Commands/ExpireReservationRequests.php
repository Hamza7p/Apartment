<?php

namespace App\Console\Commands;

use App\Enums\Reservation\ReservationStatus;
use App\Models\ReservationRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireReservationRequests extends Command
{
    protected $signature = 'reservations:expire-requests';

    protected $description = 'Expire reservation requests that are in the past';

    public function handle(): int
    {
        $today = Carbon::today();

        $count = ReservationRequest::where('status', ReservationStatus::PENDING->value)
            ->whereDate('start_date', '<', $today)
            ->update([
                'status' => ReservationStatus::EXPIRED->value,
            ]);

        $this->info("Expired {$count} reservation requests.");

        return Command::SUCCESS;
    }
}
