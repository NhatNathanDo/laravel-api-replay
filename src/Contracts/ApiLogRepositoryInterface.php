<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Contracts;

use Storage\ApiReplay\DTOs\ApiLogDTO;

interface ApiLogRepositoryInterface
{
    public function store(ApiLogDTO $log): string;

    public function find(string $uuid): ?object;

    public function filter(array $filters): array;

    public function paginate(int $perPage = 15);
}
