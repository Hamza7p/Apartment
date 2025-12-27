<?php

namespace App\Http\Controllers;

use App\Filters\NotificationFilter;
use App\Http\Resources\Notification\NotificationList;
use App\Http\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getUserNotifications($user, false);

        return new NotificationList($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
