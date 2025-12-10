<?php

namespace App\Http\Services;

use App\Http\Services\Base\CrudService;
use App\Models\User;

class AuthService //extends CrudService
{
    public function register(array $data)
    {
        $user = User::query()->create($data);
        return [
            'token' => $this->createToken($user),
            'user' => $user,
        ];
    }

    public function login(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();
        if (!$user) {
            throw new \Exception('User not found');
        }
        return [
            'token' => $this->createToken($user),
            'user' => $user,
        ];
    }

    public function createToken(User $user)
    {
        $token = $user->createToken('authToken');
        return $token->plainTextToken;
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();
    }

    public function me(User $user)
    {
        return User::query()->find($user->id);
    }

}
