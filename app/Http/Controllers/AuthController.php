<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\Auth\LoginDetails;
use App\Http\Resources\User\UserDetails;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService =  $authService;
        $this->middleware('auth:sanctum')->only(['logout', 'me']);
    }

    public function register(RegisterRequest $request): LoginDetails
    {
        $data = $this->authService->register($request->all());

        return new LoginDetails($data);
    }

    public function login(LoginRequest $request): LoginDetails
    {
        $data = $this->authService->login($request->all());
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
}
