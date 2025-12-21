<?php

namespace App\Http\Resources\Apartment;

use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\User\UserLight;

class ReviewDetails extends BaseJsonResource
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
            'review_id' => $this->id,
            'user' => new UserLight($this->whenLoaded('user')),
            'apartment' => new ApartmentLight($this->whenLoaded('apartment')),
        ];
    }
}
