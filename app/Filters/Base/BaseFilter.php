<?php

namespace App\Filters\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

abstract class BaseFilter implements TableName
{

    protected FilterDataProvider $dataProvider;

    public function __construct(FilterDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }


    // <editor-fold default-state="collapsed" desc="constants">

    private const PAGE_PARAM_NAME = 'page';

    private const PER_PAGE_PARAM_NAME = 'perPage';

    private const KEYWORD_PARAM_NAME = 'keyword';

    private const FILTERS_PARAM_NAME = 'filters';

    private const ORDERS_PARAM_NAME = 'orders';

    protected const DEFAULT_OPERATION = '=';

    protected const OPERATIONS_MAP = [
        'like' => FilterOperation::Like,
        '=' => FilterOperation::EQ,
        '!=' => FilterOperation::NEQ,
        '>' => FilterOperation::GT,
        '>=' => FilterOperation::GTE,
        '<' => FilterOperation::LT,
        '<=' => FilterOperation::LTE,
    ];

    // </editor-fold>

    public function apply(Builder $builder): Builder
    {
        $params = $this->dataProvider->validate($this->rules());

        $page = $params[self::PAGE_PARAM_NAME] ?? null;
        $perPage = $params[self::PER_PAGE_PARAM_NAME] ?? null;
        $filters = $params[self::FILTERS_PARAM_NAME] ?? null;
        $keyword = $params[self::KEYWORD_PARAM_NAME] ?? null;
        $orders = $params[self::ORDERS_PARAM_NAME] ?? null;

        if ($orders) {
            $builder = $this->order($builder, $orders);
        } else $builder = $this->defaultOrder($builder);

        if ($filters) {
            $builder = $this->filter($builder, $filters);
        }

        if ($keyword) {
            $builder = $this->search($builder, $this->normalizeKeyword($keyword));
        }

        return $this->paginate($builder, $page, $perPage);
    }

    abstract public function attributesMap(): array;


    // <editor-fold default-state="collapsed" desc="search">

    abstract protected function search(Builder $builder, string $keyword): Builder;

    private function normalizeKeyword($keyword): string
    {
        return strtolower(trim($keyword));
    }

    // </editor-fold>

    // <editor-fold default-state="collapsed" desc="filter">

    private function filter(Builder $builder, array $filters): Builder
    {
        foreach ($filters as $filter) {
            @['name' => $name, 'operation' => $operation, 'value' => $value] = $filter;

            $column = $this->attributesMap()[$name] ?? $name;

            $column = $this instanceof TableName ? $this->tableName() . '.' . $column : $column;

            $operation = $operation ? FilterOperation::from($operation) : FilterOperation::EQ;

            if (method_exists($this, $method = $name . 'Filter')) {
                $builder = $this->{$method}($builder, $column, $operation, $value);
            } else {
                $builder = $this->addFilter($builder, $column, $operation, $value);
            }
        }

        return $builder;
    }

    protected function addFilter(Builder $builder, string $column, ?FilterOperation $operation, mixed $value): Builder
    {
        $operation ??= FilterOperation::EQ;

        if ($value === "null") {
            $value = null;
        }

        if (is_string($value) && ctype_digit($value)) {
            $value = (int) $value;
        }

        switch ($operation) {
            case FilterOperation::In:
                //if value was like 1,2,3,54 convert to array of ids
                if (is_string($value)) {
                    $value = explode(',', urldecode($value));
                    $value = array_map('intval', $value);
                }

                if (!is_array($value)) {
                    $value = [$value];
                }

                $builder->whereIn($column, $value);
                break;
            case FilterOperation::Include:
                //if value was like 1,2,3,54 convert to array of ids
                if (is_string($value)) {
                    $value = explode(',', urldecode($value));
                    $value = array_map('intval', $value);
                }

                if (!is_array($value)) {
                    $value = [$value];
                }

                $query = clone $builder;
                $builder->whereIn($column, $value);
                $builder->union($query);
                break;

            case FilterOperation::NotIn:
                //if value was like 1,2,3,54 convert to array of ids
                if (is_string($value)) {
                    $value = explode(',', urldecode($value));
                    $value = array_map('intval', $value);
                }

                if (!is_array($value)) {
                    $value = [$value];
                }

                $builder->whereNotIn($column, $value);
                break;

            default:
                $operation = array_search($operation, self::OPERATIONS_MAP) ?? self::DEFAULT_OPERATION;

                $builder->where($column, $operation, $value);
                break;
        }

        return $builder;
    }

    // </editor-fold>

    // <editor-fold default-state="collapsed" desc="order">

    private function order(Builder $builder, array $orders): Builder
    {
        foreach ($orders as $order) {
            @['name' => $name, 'direction' => $direction] = $order;

            $column = $this->attributesMap()[$name] ?? $name;

            $column = $this instanceof TableName ? $this->tableName() . '.' . $column : $column;

            if (method_exists($this, $method = $name . 'Order')) {
                $builder = $this->{$method}($builder, $column, $direction);
            } else {
                $builder = $this->addOrder($builder, $column, $direction);
            }
        }

        return $builder;
    }

    protected function addOrder(Builder $builder, string $column, ?string $direction = 'asc'): Builder
    {
        $direction ??= 'asc';
        return $builder->orderBy($column, $direction);
    }

    abstract function defaultOrder(Builder $builder): Builder;

    // </editor-fold>

    // <editor-fold default-state="collapsed" desc="pagination">

    private function paginate(Builder $builder, ?int $page, ?int $perPage): Builder
    {
        if ($perPage && $perPage == -1) {
            return $builder;
        }

        if (!$perPage || $perPage == -1) {
            $perPage = $builder->getModel()->getPerPage();
        }

        if ($perPage > 1000) {
            $perPage = 1000;
        }

        return $builder->skip($perPage * ($page - 1))->take($perPage);
    }

    // </editor-fold>

    // <editor-fold default-state="collapsed" desc="rules">

    public function rules(): array
    {
        return [
            self::PAGE_PARAM_NAME => ['integer'],
            self::PER_PAGE_PARAM_NAME => ['integer'],

            self::KEYWORD_PARAM_NAME => ['string'],

            self::ORDERS_PARAM_NAME . '.*.name' => ['required', 'string', Rule::in($this->getAttributesNames())],
            self::ORDERS_PARAM_NAME . '.*.direction' => [Rule::in(['asc', 'desc'])],

            self::FILTERS_PARAM_NAME . '.*.name' => ['required', 'string', Rule::in($this->getAttributesNames())],
            self::FILTERS_PARAM_NAME . '.*.operation' => [new Enum(FilterOperation::class)],
            self::FILTERS_PARAM_NAME . '.*.value' => ['nullable'],
        ];
    }

    private function getAttributesNames(): array
    {
        $names = [];
        foreach ($this->attributesMap() as $index => $value) {
            if (is_numeric($index)) {
                $names[] = $value;
            } else {
                $names[] = $index;
            }
        }
        return $names;
    }

    // </editor-fold>
}
