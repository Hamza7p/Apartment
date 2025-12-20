<?php

namespace App\Filters;

use App\Filters\Base\BaseFilter;
use Illuminate\Database\Eloquent\Builder;


class UserFilter extends BaseFilter
{
    public function attributesMap(): array
    {
        return [
            'phone',
            'first_name',
            'last_name',
            'date_of_birth',
            'username',
            'role',
            'status',
            'verified_at',
        ];
    }

    protected function search(Builder $builder, string $keyword): Builder
    {
        $locale = app()->getLocale();

        $keyword = '%' . $keyword . '%';

        $builder->where(function ($query) use ($locale, $keyword) {
            /** @var Builder $query */
            return $query->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?",[$keyword]);
        });

        return $builder;
    }

    function defaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy($this->tableName() . '.' . 'created_at', 'desc');
    }

    public function tableName()
    {
        return 'users';
    }

}