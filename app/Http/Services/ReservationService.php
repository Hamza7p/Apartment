<?php

namespace App\Http\Services;

use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Notifications\Reservation\ReservationAcceptedNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ReservationService extends CrudService
{
    private ReservationConflictService $reservationConflictService;

    private ApartmentAvailabilityService $apartmentAvailabilityService;

    public function __construct(
        ReservationConflictService $reservationConflictService,
        ApartmentAvailabilityService $apartmentAvailabilityService
    ) {
        $this->reservationConflictService = $reservationConflictService;
        $this->apartmentAvailabilityService = $apartmentAvailabilityService;
    }

    protected function getModelClass(): string
    {
        return Reservation::class;
    }

    public function accept($reservation_request_id)
    {
        $reservation_request = ReservationRequest::findOrFail($reservation_request_id);

        return DB::transaction(function () use ($reservation_request) {
            $apartment = Apartment::findOrFail($reservation_request->apartment_id);

            $start = Carbon::parse($reservation_request->start_date);
            $end = Carbon::parse($reservation_request->end_date);

            $days = $start->diffInDays($end);
            $total_amount = $apartment->price * $days;

            if ($reservation_request->status->value !== ReservationStatus::PENDING->value) {
                throw new \Exception(__('errors.unauthorized'));
            }

            // ✅ Update availability via service
            $newAvailability = $this->apartmentAvailabilityService->applyReservation(
                $apartment,
                $start,
                $end
            );

            $apartment->update([
                'availability' => $newAvailability,
            ]);

            $reservation = $this->create([
                'reservation_request_id' => $reservation_request->id,
                'user_id' => $reservation_request->user_id,
                'apartment_id' => $apartment->id,
                'start_date' => $start,
                'end_date' => $end,
                'status' => ReservationStatus::ACTIVE->value,
                'total_amount' => $total_amount,
            ]);

            Notification::send(
                $reservation_request->user,
                new ReservationAcceptedNotification($reservation)
            );

            $reservation_request->update([
                'status' => ReservationStatus::ACCEPTED->value,
            ]);

            $this->reservationConflictService->rejectConflictingRequests(
                $apartment->id,
                $start,
                $end,
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

    public function getMyReservatoins()
    {
        $user = Auth::user();

        $reservations = $user->reservations;

        return $reservations;
    }

    public function getMyApartmentReservations()
    {
        $user = Auth::user();
        $apartmentIds = $user->apartments()->pluck('id');

        return Reservation::whereIn('apartment_id', $apartmentIds)->get();

    }
}
