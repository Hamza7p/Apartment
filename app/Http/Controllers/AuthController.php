<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\Auth\LoginDetails;
use App\Http\Resources\User\UserDetails;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService =  $authService;
        $this->middleware('auth:sanctum')->only(['logout', 'me']);
        $this->middleware('throttle:10,1')->only(['sendOtp', 'verifyOtp']);
    }

    public function register(RegisterRequest $request): LoginDetails
    {
        $data = $this->authService->register($request->all());

        return new LoginDetails($data);
    }

    public function login(LoginRequest $request): LoginDetails
    {
        $data = $this->authService->login($request->validated());
        return new LoginDetails($data);
    }

    public function logout()
    {
        $user = Auth::user();
        $this->authService->logout($user);
        return response()->noContent();
    }

    public function me()
    {
        $user = $this->authService->me(Auth::user());
        return new UserDetails($user);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword( $request->validated());
        return response()->noContent();
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $this->authService->sendOtp($request->validated());
        return response()->noContent();
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->authService->verifyOtp($request->validated());
    }
}
