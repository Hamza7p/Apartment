<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOtpRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService =  $authService;
    }

    // public function register(RegisterRequest $request): UserDetails
    // {
    //     $user = $this->authService->register($request->getData());

    //     return new UserDetails($user);
    // }

    public function sendOtp(SendOtpRequest $request)
    {
        $data = $request->all();
        $this->authService->sendOtp($data['phone']);

        return response()->noContent();
    }
}
