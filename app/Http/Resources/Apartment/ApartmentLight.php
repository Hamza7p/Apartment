<?php

namespace App\Http\Resources\Apartment;

use App\Http\Resources\Base\BaseJsonResource;

class ApartmentLight extends BaseJsonResource
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
            'title' => $this->title,

        ];
    }
}
