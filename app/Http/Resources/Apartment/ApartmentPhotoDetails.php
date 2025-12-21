<?php

namespace App\Http\Resources\Apartment;

use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\Medium\MediumLight;

class ApartmentPhotoDetails extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [

        ];
    }

    public function toArray($request): array
    {
        return [
            'medium' => new MediumLight($this->medium),
            'apartment' => new ApartmentLight($this->apartment),
            'order' => $this->order,
            'is_main' => $this->is_main,
        ];
    }
}
