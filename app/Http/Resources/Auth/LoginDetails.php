<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this['token'],
            'user' => new UserDetails($this['user']),
        ];
    }
}
