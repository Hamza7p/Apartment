<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Base\BaseJsonResource;
use Illuminate\Http\Request;

class UserLight extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected static function relations(): array
    {
        return [
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
        ];
    }
}
