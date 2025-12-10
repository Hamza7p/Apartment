<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Base\BaseJsonResource;
use Illuminate\Http\Request;

class UserDetails extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected static function relations(): array
    {
        return [
            'role',
            'idPhoto',
            'personalPhoto',
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'date_of_birth' => $this->date_of_birth,
            'role_id' => $this->role_id,
            'id_photo' => $this->id_photo,
            'personal_photo' => $this->personal_photo,
            'status' => $this->status,
            'verified_at' => $this->verified_at,
        ];
    }
}
