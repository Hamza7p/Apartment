<?php

namespace App\Http\Services;

use App\Enums\User\UserStatus;
use App\Models\User;
use App\Http\Services\Base\CrudService;
use App\Models\Base\BaseModel;

class UserService extends CrudService
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    public function create(array $data): BaseModel
    {
        $data = array_merge([
            ...$data,
            'status' => UserStatus::approved->value,
        ]);
        return parent::create($data);
    }
}
