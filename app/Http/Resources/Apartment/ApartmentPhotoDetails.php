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
            'medium' => new MediumLight($this),
            'order' => $this->pivot->order ?? null,
            'is_main' => $this->pivot->is_main ?? null,
        ];
    }
}
