<?php

namespace App\Http\Resources\Base;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    public static $wrap = null;

    public function __construct($resource)
    {
        if ($resource instanceof BaseModel) {
            $resource = self::loadRelations($resource);
        }

        parent::__construct($resource);
    }

    protected static function relations(): array
    {
        return [];
    }

    protected static function relationsCount(): array
    {
        return [];
    }

    protected static function relationsSum(): array
    {
        return [];
    }

    public static function query(Builder $builder): AnonymousResourceCollection
    {
        $builder = self::withRelations($builder);

        $perPage = $builder->getQuery()->unions ? $builder->getQuery()->unionLimit : $builder->getQuery()->limit;
        $offset = $builder->getQuery()->unions ? $builder->getQuery()->unionOffset : $builder->getQuery()->offset;
        $total = $builder->toBase()->getCountForPagination();

        // when perPage null return without pagination
        if (! $perPage) {
            return parent::collection($builder->get());
        }

        $page = (int) ceil($offset / $perPage);
        $lastPage = max((int) ceil($total / $perPage), 1);

        $data = parent::collection($builder->get());
        $data->additional = [
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => $lastPage,
            'total' => $total,
        ];

        return $data;
    }

    public static function queryWithRelations(Builder $builder): Builder
    {
        return self::withRelations($builder);
    }

    public static function queryMorphMany(MorphMany $builder): AnonymousResourceCollection
    {
        $perPage = $builder->getBaseQuery()->limit;

        // when perPage null return without pagination
        if (! $perPage) {
            return parent::collection($builder->get());
        }

        $offset = $builder->getBaseQuery()->offset;
        $total = $builder->toBase()->getCountForPagination();

        $page = (int) ceil($offset / $perPage);
        $lastPage = max((int) ceil($total / $perPage), 1);

        $data = parent::collection($builder->get());
        $data->additional = [
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => $lastPage,
            'total' => $total,
        ];

        return $data;
    }

    private static function withRelations(Builder $builder): Builder
    {
        $builder->with(static::relations());
        $builder->withCount(static::relationsCount());
        $unions = $builder->getQuery()->unions;
        if ($unions) {
            foreach ($unions as $union) {
                foreach ($union as $unionBuilder) {
                    if ($unionBuilder) {
                        $unionBuilder->with(static::relations());
                        $unionBuilder->withCount(static::relationsCount());
                    }
                }
            }
        }
        foreach (static::relationsSum() as $relation => $colum) {
            $builder->withSum($relation, $colum);
            if ($unions) {
                foreach ($unions as $union) {
                    foreach ($union as $unionBuilder) {
                        if ($unionBuilder) {
                            $unionBuilder->withSum($relation, $colum);
                        }
                    }
                }
            }
        }

        return $builder;
    }

    private static function loadRelations(BaseModel $model): BaseModel
    {
        $model->loadMissing(static::relations());

        if (count(static::relationsCount()) > 0) {
            $model->loadCount(static::relationsCount());
        }

        foreach (static::relationsSum() as $relation => $colum) {
            $model->loadSum($relation, $colum);
        }

        return $model;
    }

    public function translate($field)
    {
        $lang = app()->getLocale();

        return $field[$lang] ?? null;
    }
}
