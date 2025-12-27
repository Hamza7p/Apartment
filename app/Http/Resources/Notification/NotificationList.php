<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\Base\BaseJsonResource;

class NotificationList extends BaseJsonResource
{
    protected static function relations(): array
    {
        return [

        ];
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            {{ attributes }}

        ];
    }
}
