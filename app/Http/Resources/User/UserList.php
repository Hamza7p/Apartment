<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\Medium\MediumLight;

class UserList extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [
            'personalPhoto',
        ];
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'date_of_birth' => $this->date_of_birth,
            'role' => $this->role,
            'personal_photo' => new MediumLight($this->whenLoaded('personalPhoto')),
            'status' => $this->status,
            'verified_at' => $this->verified_at,
        ];
    }
}
