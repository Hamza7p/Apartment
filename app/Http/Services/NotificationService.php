<?php 

namespace App\Http\Services;

use App\Filters\Base\BaseFilter;
use App\Http\Services\Base\CrudService;
use App\Models\Notification;
use App\Models\User;
<<<<<<< HEAD
use Illuminate\Pagination\LengthAwarePaginator;
=======
>>>>>>> origin/main

class NotificationService extends CrudService
{
<<<<<<< HEAD
    /**
     * Send a notification to a user.
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
     * @param  array<User|int>  $users
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
     * @param  bool|null  $read  Filter by read status (null = all)
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
     */
    public function getUnreadCount(User|int $user): int
=======
    protected function getModelClass(): string
>>>>>>> origin/main
    {
        return Notification::class;
    }

    public function getUserNotifications(User $user, BaseFilter $filter)
    {
        return parent::getAll($filter, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    public function markAsRead(User $user)
    {
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getUnreadCount(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
<<<<<<< HEAD

    /**
     * Mark notification as read.
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
     */
    public function delete(int $notificationId, User|int $user): bool
    {
        $userId = is_int($user) ? $user : $user->id;

        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
=======
}
>>>>>>> origin/main
