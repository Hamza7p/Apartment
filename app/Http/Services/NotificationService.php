<?php 

namespace App\Http\Services;

use App\Filters\Base\BaseFilter;
use App\Http\Services\Base\CrudService;
use App\Models\Notification;
use App\Models\User;

class NotificationService extends CrudService
{
    protected function getModelClass(): string
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
}