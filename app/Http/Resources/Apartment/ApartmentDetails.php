<?php

namespace App\Http\Resources\Apartment;

use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\User\UserLight;

class ApartmentDetails extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [

        ];
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner' => new UserLight($this->whenLoaded('owner')),
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'governorate' => [
                'value' => $this->governorate->value,
                'en' => $this->governorate->label('en'),
                'ar' => $this->governorate->label('ar'),
            ],
            'city' => $this->city,
            'address' => $this->address,
            'status' => $this->status,
            'number_of_room' => $this->number_of_room,
            'number_of_bathroom' => $this->number_of_bathroom,
            'area' => $this->area,
            'floor' => $this->floor,
            'created_at' => $this->created_at->format('Y-m-d'),
            'photos' => ApartmentPhotoDetails::collection($this->whenLoaded('photos')),

        ];
    }
}
