<?php

namespace App\Http\Services;

use App\Enums\Notification\NotificationType;
use App\Http\Resources\Apartment\ApartmentLight;
use App\Http\Resources\User\UserLight;
use App\Http\Services\Base\CrudService;
use App\Models\Base\BaseModel;
use App\Models\ReservationRequest;
use Illuminate\Support\Facades\Auth;

class ReservationRequestService extends CrudService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
        $this->notificationService->send(
            $owner,
            NotificationType::RESERVATION_REQUEST->value,
            __('notifications.new_reservation_request'),
            __('notifications.new_reservation-request_body'),
            [
                'user' => new UserLight($user),
                'apartment' => new ApartmentLight($apartment),
            ]
        );

        return $reservation_request;

    }
}
