<?php

namespace App\Http\Services\Base;

use App\Filters\Base\BaseFilter;
use App\Http\Requests\Base\BaseFromRequest;
use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class CrudService
{
    abstract protected function getModelClass(): string;

    protected function getQuery(bool $withTrashed = false): Builder
    {
        $model = $this->getModelClass();

        /** @var Builder $query */
        $query = $model::query();

        if ($withTrashed && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query->withTrashed();
        }

        return $query;
    }

    // public function getAll(?BaseFilter $filter = null, bool $withTrashed = false): Builder
    // {
    //     $query = $this->getQuery($withTrashed);
    //     return $filter->apply($query);
    // }

    // public function find(mixed $id): BaseModel
    // {
    //     if ($id instanceof BaseModel) {
    //         return $id;
    //     } else {
    //         return $this->getQuery()->findOrFail($id);
    //     }
    // }

    // public function create(array $data): BaseModel
    // {
    //     return $this->getQuery()->create($data);
    // }

    // public function update(mixed $id, array $data): BaseModel
    // {
    //     $model = $this->find($id);
    //     $model->update($data);
    //     return $model;
    // }

    // public function delete(mixed $id): void
    // {
    //     $this->find($id)->delete();
    // }
}
