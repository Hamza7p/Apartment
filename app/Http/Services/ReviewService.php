<?php

namespace App\Http\Services;

use App\Filters\Base\BaseFilter;
use App\Http\Services\Base\CrudService;
use App\Models\Base\BaseModel;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReviewService extends CrudService
{
    protected function getModelClass(): string
    {
        return Review::class;
    }

    // public function getAll(?BaseFilter $filter = null, bool $withTrashed = false): Builder
    // {
    //     return parent::getAll();
    // }

    public function create(array $data): BaseModel
    {
        $userId = Auth::user()->id;
        $data['user_id'] = $userId;

        return parent::create($data);
    }
}
