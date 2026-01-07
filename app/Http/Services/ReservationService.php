<?php

namespace App\Http\Services;

use App\Enums\Apartment\ApartmentStatus;
use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Notifications\Reservation\ReservationAcceptedNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ReservationService extends CrudService
{
    private ReservationConflictService $reservationConflictService;

    public function __construct(
        ReservationConflictService $reservationConflictService)
    {
        $this->reservationConflictService = $reservationConflictService;
    }

    protected function getModelClass(): string
    {
        return Reservation::class;
    }

    public function accept($reservation_request_id)
    {
        $reservation_request = ReservationRequest::find($reservation_request_id);

        return DB::transaction(function () use ($reservation_request) {
            $apartment = Apartment::find($reservation_request->apartment_id);

            $start = Carbon::parse($reservation_request->start_date);
            $end = Carbon::parse($reservation_request->end_date);
            $days = $start->diffInDays($end);
            $total_amount = $apartment->price * $days;

            if ($reservation_request->status->value !== ReservationStatus::PENDING->value) {
                throw new \Exception(__('errors.unauthorized'));
            }

            // --- Update availability JSON ---
            $availability = $apartment->availability;

            if (! is_array($availability) || empty($availability)) {
                $availability = [
                    [
                        'from' => now()->toDateTimeString(),
                        'to' => '2099-12-31',
                    ],
                ];
            }

            // Now $availability is always an array
            $new_availability = [];

            foreach ($availability as $period) {
                $period_start = Carbon::parse($period['from']);
                $period_end = Carbon::parse($period['to']);

                // If no overlap with current reservation, keep it
                if ($end->lt($period_start) || $start->gt($period_end)) {
                    $new_availability[] = $period;
                } else {
                    // Split overlapping period
                    if ($period_start->lt($start)) {
                        $new_availability[] = [
                            'from' => $period_start->toDateTimeString(),
                            'to' => $start->subDay()->toDateTimeString(),
                        ];
                    }
                    if ($period_end->gt($end)) {
                        $new_availability[] = [
                            'from' => $end->addDay()->toDateTimeString(),
                            'to' => $period_end->toDateTimeString(),
                        ];
                    }
                }
            }

            // Save updated availability after reservation
            $apartment->availability = $new_availability;
            $apartment->status = ApartmentStatus::RESERVED->value;
            $apartment->save();

            // --- Create reservation ---
            $reservation = $this->create([
                'reservation_request_id' => $reservation_request->id,
                'user_id' => $reservation_request->user_id,
                'apartment_id' => $reservation_request->apartment_id,
                'start_date' => $reservation_request->start_date,
                'end_date' => $reservation_request->end_date,
                'status' => ReservationStatus::ACTIVE->value,
                'total_amount' => $total_amount,
            ]);

            Notification::send($reservation_request->user, new ReservationAcceptedNotification($reservation));

            ReservationRequest::where('id', $reservation_request->id)
                ->update(['status' => ReservationStatus::ACCEPTED->value]);

            $this->reservationConflictService->rejectConflictingRequests(
                $reservation_request->apartment_id,
                $reservation_request->start_date,
                $reservation_request->end_date,
                $reservation_request->id
            );

            return $reservation;
        });
    }

    public function reject($reservation_request_id)
    {
        $reservation_request = ReservationRequest::find($reservation_request_id);
        if (! $reservation_request) {
            throw new Exception(__('errors.not_found', ['model' => app()->getLocale() === 'en' ? 'reservation_request' : 'طلب الحجز']));
        }

        if ($reservation_request->status->value === ReservationStatus::ACCEPTED->value) {
            throw new Exception(__('errors.unauthorized'));
        }

        $reservation_request->status = ReservationStatus::REJECTED->value;
        $reservation_request->saveQuietly();

        Notification::send($reservation_request->user, $reservation_request);

        return $reservation_request;
    }
}
