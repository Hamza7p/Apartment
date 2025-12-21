<?php

namespace App\Filters\Base;

use Illuminate\Foundation\Http\FormRequest;

class HttpFilterDataAdapter implements FilterDataProvider
{
    protected FormRequest $request;

    public function __construct(FormRequest $request)    {
        $this->request = $request;
    }

    public function getPage(): ?int
    {
        return $this->request->input('page');
    }

    public function getPerPage(): ?int
    {
        return $this->request->input('perPage');
    }

    public function getKeyword(): ?string
    {
        return $this->request->input('keyword');
    }

    public function getFilters(): ?array
    {
        return $this->request->input('filters');
    }

    public function getOrders(): ?array
    {
        return $this->request->input('orders');
    }

    public function validate(array $rules): array
    {
        return $this->request->validate($rules);
    }
}
