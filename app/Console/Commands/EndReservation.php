<?php

namespace App\Console\Commands;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EndReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End reservation  that are in the past';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $count = Reservation::where('status', ReservationStatus::PENDING->value)
            ->whereDate('end_date', '<', $today)
            ->update([
                'status' => ReservationStatus::ENDED->value,
            ]);

        $this->info("End {$count} reservation .");

        return Command::SUCCESS;
    }
}
