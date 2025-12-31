<?php

namespace App\Http\Resources\Reservation;

use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\User\UserLight;

class ReservationModificationDetails extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [
            'reservation',
            'user',
        ];
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'old value' => $this->old_value,
            'new value' => $this->new_value,
            'status' => $this->status,
            'reservation' => new ReservationDetails($this->whenLoaded('reservation')),
            'user' => new UserLight($this->whenLoaded('user')),

        ];
    }
}
