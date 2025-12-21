<?php

namespace App\Http\Services;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService
{
    /**
     * Send a notification to a user.
     *
     * @param User|int $user
     * @param string $type
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @param string|null $fcmToken
     * @return void
     */
    public function send(
        User|int $user,
        string $type,
        string $title,
        string $body,
        ?array $data = null,
        ?string $fcmToken = null
    ): void {
        SendNotificationJob::dispatch($user, $type, $title, $body, $data, $fcmToken);
    }

    /**
     * Send notifications to multiple users.
     *
     * @param array<User|int> $users
     * @param string $type
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @return void
     */
    public function sendToMany(
        array $users,
        string $type,
        string $title,
        string $body,
        ?array $data = null
    ): void {
        foreach ($users as $user) {
            $this->send($user, $type, $title, $body, $data);
        }
    }

    /**
     * Get user's notifications.
     *
     * @param User|int $user
     * @param bool|null $read Filter by read status (null = all)
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserNotifications(
        User|int $user,
        ?bool $read = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $userId = is_int($user) ? $user : $user->id;

        $query = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($read !== null) {
            $query->where('read', $read);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get unread notifications count for a user.
     *
     * @param User|int $user
     * @return int
     */
    public function getUnreadCount(User|int $user): int
    {
        $userId = is_int($user) ? $user : $user->id;

        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Mark notification as read.
     *
     * @param int $notificationId
     * @param User|int $user
     * @return bool
     */
    public function markAsRead(int $notificationId, User|int $user): bool
    {
        $userId = is_int($user) ? $user : $user->id;

        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param User|int $user
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead(User|int $user): int
    {
        $userId = is_int($user) ? $user : $user->id;

        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete a notification.
     *
     * @param int $notificationId
     * @param User|int $user
     * @return bool
     */
    public function delete(int $notificationId, User|int $user): bool
    {
        $userId = is_int($user) ? $user : $user->id;

        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}

