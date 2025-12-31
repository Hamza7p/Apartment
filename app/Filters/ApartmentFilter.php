<?php

namespace App\Filters;

use App\Filters\Base\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class ApartmentFilter extends BaseFilter
{
    public function attributesMap(): array
    {
        return [
            'user_id',
            'title',
            'description',
            'price',
            'currency',
            'rate',
            'governorate',
            'city',
            'address',
            'status',
            'number_of_room',
            'number_of_bathroom',
            'area',
            'floor',
        ];
    }

    protected function search(Builder $builder, string $keyword): Builder
    {
        $locale = app()->getLocale();
        $keyword = '%'.$keyword.'%';

        return $builder->where(function (Builder $query) use ($locale, $keyword) {
            $query->where("title->$locale", 'LIKE', $keyword)
                ->orWhere("description->$locale", 'LIKE', $keyword)
                ->orWhere("city->$locale", 'LIKE', $keyword)
                ->orWhere("address->$locale", 'LIKE', $keyword);
        });

    }

    public function defaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy($this->tableName().'.'.'created_at', 'desc');
    }

    public function tableName()
    {
        return 'apartments';
    }
}
