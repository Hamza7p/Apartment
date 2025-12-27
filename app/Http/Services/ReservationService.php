<?php

namespace App\Http\Services;

use App\Enums\Notification\NotificationType;
use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ReservationService extends CrudService
{
    private ReservationConflictService $reservationConflictService;

    private NotificationService $notificationService;

    public function __construct(
        ReservationConflictService $reservationConflictService,
        NotificationService $notificationService
    ) {
        $this->reservationConflictService = $reservationConflictService;
        $this->notificationService = $notificationService;
    }

    protected function getModelClass(): string
    {
        return Reservation::class;
    }

    public function accept($reservation_request_id)
    {
        $reservation_request = ReservationRequest::find($reservation_request_id);

        return DB::transaction(function () use ($reservation_request) {
            if (
                $this->reservationConflictService->hasConflict(
                    $reservation_request->apartment_id,
                    $reservation_request->start_date,
                    $reservation_request->end_date,
                )
            ) {
                throw new Exception(__('errors.apartment_not_available'));
            }
            $apartment = Apartment::find($reservation_request->apartment_id);
            $start = Carbon::parse($reservation_request->start_date);
            $end = Carbon::parse($reservation_request->end_date);
            $days = $start->diffInDays($end);
            $total_amount = $apartment->price * $days;

            $apartment->update(['available_at' => $end->addDay()]);

            $reservation = $this->create([
                'reservation_request_id' => $reservation_request->id,
                'user_id' => $reservation_request->user_id,
                'apartment_id' => $reservation_request->apartment_id,
                'start_date' => $reservation_request->start_date,
                'end_date' => $reservation_request->end_date,
                'total_amount' => $total_amount,
            ]);

            $this->notificationService->send(
                $reservation_request->user_id,
                NotificationType::RESERVATION_APPROVED->value,
                __('notifications.reservation_accepted'),
                __('notifications.reservation_accepted_body'),
            );

            ReservationRequest::where('id', $reservation_request->id)->update(['status' => ReservationStatus::ACCEPTED->value]);

            ReservationRequest::where('status', ReservationStatus::PENDING->value)
                ->where('id', '!=', $reservation_request->id)
                ->where(function ($q) use ($reservation_request) {
                    $q->where('start_date', '<=', $reservation_request->end_date)
                        ->where('end_date', '>=', $reservation_request->start_date);
                })->update(['status' => ReservationStatus::REJECTED->value]);

            return $reservation;
        });
    }
}
