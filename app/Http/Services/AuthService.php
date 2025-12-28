<?php

namespace App\Http\Services;

use App\Enums\Role\RoleName;
use App\Enums\User\UserStatus;
use App\Models\User;
use App\Notifications\RegistrationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

class AuthService  //extends CrudService
{
    public function register(array $data)
    {
        $data = array_merge([
            ...$data,
            'role' => RoleName::user->value,
            'status' => UserStatus::pending->value,
        ]);
        $user = User::query()->create($data);
        
        Notification::send(User::admins()->get(), new RegistrationNotification($user));
       
        return [
            'token' => $this->createToken($user),
            'user' => $user,
        ];
    }

    public function login(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            abort(401, __('exceptions.invalid_credentials'));
        }

        return [
            'token' => $this->createToken($user),
            'user' => $user,
        ];
    }

    public function createToken(User $user)
    {
        $token = $user->createToken('authToken')->plainTextToken;

        return $token;
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
