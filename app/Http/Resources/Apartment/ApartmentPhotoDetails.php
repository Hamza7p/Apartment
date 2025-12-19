<?php

namespace App\Http\Resources\Apartment;

use App\Http\Resources\Base\BaseJsonResource;

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
            'media_id' => $this->id,
            'apartment_id' => $this->pivot->apartment_id ?? null,
            'order' => $this->pivot->order ?? null,
            'is_main' => $this->pivot->is_main ?? false,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
