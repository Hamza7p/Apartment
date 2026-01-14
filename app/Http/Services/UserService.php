<?php

namespace App\Http\Services;

use App\Enums\User\UserStatus;
use App\Filters\Base\BaseFilter;
use App\Models\User;
use App\Http\Services\Base\CrudService;
use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserService extends CrudService
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    public function getAll(?BaseFilter $filter = null): Builder
    {
        $users = parent::getAll($filter);
        return $users->whereNot('id', Auth::user()->id);
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
