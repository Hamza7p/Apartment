<?php

namespace App\Filters\Base;

interface FilterDataProvider
{
    public function getPage(): ?int;

    public function getPerPage(): ?int;

    public function getKeyword(): ?string;

    public function getFilters(): ?array;

    public function getOrders(): ?array;

    public function validate(array $rules): array;
}
