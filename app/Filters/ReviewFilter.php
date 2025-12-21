<?php

namespace App\Filters;

use App\Filters\Base\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class ReviewFilter extends BaseFilter
{
    public function attributesMap(): array
    {
        return [
            'rate' => 'rate',
            'user_id' => 'user_id',
            'apartment_id' => 'apartment_id',
        ];
    }

    protected function search(Builder $builder, string $keyword): Builder
    {
        $locale = app()->getLocale();
        $keyword = '%'.$keyword.'%';

        return $builder->where(function (Builder $query) use ($locale, $keyword) {
            $query->where("comment->$locale", 'LIKE', $keyword);
        });
    }

    public function defaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy($this->tableName().'.created_at', 'desc');
    }

    public function tableName()
    {
        return 'reviews';
    }
}
