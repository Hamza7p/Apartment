<?php

namespace App\Http\Services;

use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\Base\BaseModel;
use App\Models\ReservationRequest;
use App\Notifications\Reservation\ReservationRequestNotification as ReservationReservationRequestNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ReservationRequestService extends CrudService
{
    protected function getModelClass(): string
    {
        return ReservationRequest::class;
    }

    public function create(array $data): BaseModel
    {
        $apartment = Apartment::findOrFail($data['apartment_id']);
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        $days = $start->diffInDays($end);
        $total_amount = $apartment->price * $days;

        $data['user_id'] = Auth::user()->id;
        $data['total_amount'] = $total_amount;

        $reservation_request = parent::create($data);
        $user = $reservation_request->user;
        $apartment = $reservation_request->apartment;
        $owner = $apartment->owner;
        Notification::send($apartment->owner, new ReservationReservationRequestNotification($reservation_request));

        return $reservation_request;

    }

    public function cancel($id)
    {
        $reservation_request = ReservationRequest::find($id);
        if (! $reservation_request) {
            throw new Exception(__('errors.not_found'));
        }

        $reservation_request->status = ReservationStatus::CANCELLED->value;
        $reservation_request->saveQuietly();
    }

    public function getSendReservationRequest()
    {
        $user = Auth::user();
        $resevationRequests = $user->reservationRequests;

        return $resevationRequests;
    }

    public function getReceiveReservationRequest()
    {
        $user = Auth::user();

        $apartmentIds = $user->apartments()->pluck('id');

        return ReservationRequest::whereIn('apartment_id', $apartmentIds)->get();
    }
}
