<?php

namespace App\Filters;

use App\Filters\Base\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class ReservationModificationFilter extends BaseFilter
{
    public function attributesMap(): array
    {
        return [
            'reservation_id',
            'type',
            'old_value',
            'new_value',
            'requested_by',
            'status',

        ];
    }

    protected function search(Builder $builder, string $keyword): Builder
    {
        $locale = app()->getLocale();

        $keyword = '%'.$keyword.'%';

        $builder->where(function ($query) {
            /** @var Builder $query */
        });

        return $builder;
    }

    public function defaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy($this->tableName().'.'.'created_at', 'desc');
    }

    public function tableName()
    {
        return 'reservation_modifications';
    }
}
