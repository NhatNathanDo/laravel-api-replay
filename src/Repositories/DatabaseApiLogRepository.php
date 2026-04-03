<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Repositories;

use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\DTOs\ApiLogDTO;
use Storage\ApiReplay\Models\ApiLog;

class DatabaseApiLogRepository implements ApiLogRepositoryInterface
{
    public function store(ApiLogDTO $log): string
    {
        $model = ApiLog::create($log->toArray());
        return $model->id;
    }

    public function find(string $uuid): ?ApiLog
    {
        return ApiLog::find($uuid);
    }

    public function filter(array $filters): array
    {
        return ApiLog::query()
            ->when($filters['path'] ?? null, fn($q, $v) => $q->where('path', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('response_status', $v))
            ->latest('created_at')
            ->get()
            ->toArray();
    }

    public function paginate(int $perPage = 15)
    {
        return ApiLog::query()->latest('created_at')->paginate($perPage);
    }
}
