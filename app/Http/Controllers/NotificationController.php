<?php

namespace App\Http\Controllers;

use App\Filters\NotificationFilter;
use App\Http\Resources\Notification\NotificationList;
use App\Http\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
    }

    public function index(NotificationFilter $filter)
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getUserNotifications($user, $filter);

        return NotificationList::query($notifications);
    }

    public function markAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAsRead($user);

        return response()->noContent();
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json(['unread_count' => $unreadCount]);
    }
}
