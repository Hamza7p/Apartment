<?php

namespace App\Http\Services;

use App\Enums\Role\RoleName;
use App\Enums\User\UserStatus;
use App\Models\MobileOtp;
use App\Models\User;
use App\Notifications\RegistrationNotification;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class AuthService  // extends CrudService
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

    public function resetPassword(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();
        if (! $user) {
            abort(404, __('exceptions.user_not_found'));
        }
        $user->update([
            'password' => Hash::make($data['password']),
        ]);
    }

    public function sendOtp(array $data)
    {
        $this->generateOtp($data['phone']);
        // Notification::send(new SendOtpNotification($data['phone'], $data['otp']));
    }

    public function verifyOtp(array $data)
    {
        $otp = MobileOtp::where('phone', $data['phone'])->where('otp', $data['otp'])->first();
        if (! $otp) {
            abort(400, __('exceptions.invalid_otp'));
        }
        if ($otp->expires_at < now()) {
            abort(400, __('exceptions.otp_expired'));
        }
        $otp->update(['verified' => true]);

        $user = $otp->user;
        if($user){
            $this->verifyUser($user);
        }

        return $user ?? true;
    }

    public function verifyUser(User $user)
    {
        $user->update(['verified_at' => now()]);
    }

    public function generateOtp(string $phone)
    {
        $data = [
            'phone' => $phone,
            'otp' => generateOtpCode(),
            'expires_at' => now()->addMinutes(5),
        ];
        return MobileOtp::updateOrCreate(['phone' => $phone], $data);
    }
}
