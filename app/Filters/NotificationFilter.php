<?php

namespace App\Filters;


use App\Filters\Base\BaseFilter;
use Illuminate\Database\Eloquent\Builder;


class NotificationFilter extends BaseFilter
{
    public function attributesMap(): array
    {
        return [
            'read_at' => 'read_at',
            'user_id' => 'user_id',
        ];
    }

    protected function search(Builder $builder, string $keyword): Builder
    {
        $locale = app()->getLocale();

            $keyword = '%' . $keyword . '%';

            $builder->where(function ($query) use ($locale, $keyword) {
                    /** @var Builder $query */

            });

            return $builder;
    }

    function defaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy($this->tableName() . '.' . 'created_at', 'desc');
    }

    public function tableName()
    {
        return '';
    }

}