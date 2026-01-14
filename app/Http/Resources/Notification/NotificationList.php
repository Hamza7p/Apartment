<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\Base\BaseJsonResource;
use App\Http\Resources\User\UserLight;

class NotificationList extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [
            'user',
        ];
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'user' => new UserLight($this->whenLoaded('user')),
            'read_at' => $this->read_at,            
        ];
    }
}
