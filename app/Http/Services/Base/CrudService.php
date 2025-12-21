<?php

namespace App\Http\Services\Base;

use App\Filters\Base\BaseFilter;
use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Builder;

abstract class CrudService
{
    abstract protected function getModelClass(): string;

    protected function getQuery(): Builder
    {
        $model = $this->getModelClass();

        /** @var Builder $query */
        $query = $model::query();

        return $query;
    }

    public function getAll(?BaseFilter $filter = null, bool $withTrashed = false): Builder
    {
        $query = $this->getQuery($withTrashed);

        return $filter->apply($query);
    }

    public function find(mixed $id): ?BaseModel
    {
        if ($id instanceof BaseModel) {
            return $id;
        } else {
            return $this->getQuery()->findOrFail($id);
        }
    }

    public function create(array $data): BaseModel
    {
        return $this->getQuery()->create($data);
    }

    public function update(mixed $id, array $data): BaseModel
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    public function delete(mixed $id): void
    {
        $this->find($id)->delete();
    }
}
