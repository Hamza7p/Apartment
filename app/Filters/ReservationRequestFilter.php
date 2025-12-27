<?php

namespace App\Filters;

use App\Filters\Base\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class ReservationRequestFilter extends BaseFilter
{
    public function attributesMap(): array
    {
        return [
            'user_id' => 'user_id',
            'apartment_id' => 'apartment_id',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'status' => 'status',

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
        return 'reservation_requests';
    }
}
