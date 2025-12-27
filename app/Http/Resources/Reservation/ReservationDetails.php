<?php

namespace App\Http\Resources\Reservation;

use App\Http\Resources\Apartment\ApartmentLight;
use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\User\UserLight;

class ReservationDetails extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [
            'user',
            'apartment',
        ];
    }

    public function toArray($request): array
    {
        return [
            'reservation_id' => $this->id,
            'user' => new UserLight($this->whenLoaded('user')),
            'apartment' => new ApartmentLight($this->whenLoaded('apartment')),
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),

        ];
    }
}
