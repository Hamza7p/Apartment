<?php

namespace App\Http\Services;

use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Base\BaseModel;
use App\Models\ReservationRequest;
use App\Notifications\Reservation\ReservationRequestNotification as ReservationReservationRequestNotification;
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
        $data['user_id'] = Auth::user()->id;
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

        $reservation_request->status = ReservationStatus::CANCELED->value;
        $reservation_request->saveQuietly();
    }
}
