<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Http\Requests\UserRequest;
use App\Http\Resources\User\UserDetails;
use App\Http\Resources\User\UserList;
use App\Http\Services\UserService;
use App\Models\User;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService =  $userService;
        $this->middleware('auth:sanctum');
        $this->authorizeResource(User::class);
    }

    public function index(UserFilter $filter)
    {
        $query = $this->userService->getAll($filter);
        return UserList::query($query);
    }

    public function store(UserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return new UserDetails($user);
    }

    public function show(User $user)
    {
        $user = $this->userService->find($user);
        return new UserDetails($user);
    }

    public function update(User $user, UserRequest $request)
    {
        $user = $this->userService->update($user, $request->validated());
        return new UserDetails($user);
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);
        return response()->noContent();
    }
}
