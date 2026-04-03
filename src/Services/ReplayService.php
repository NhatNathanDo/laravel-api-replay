<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Services;

use Illuminate\Support\Facades\Http;
use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\Models\ApiLog;
use Storage\ApiReplay\Models\ApiLogReplay;

class ReplayService
{
    public function __construct(
        protected ApiLogRepositoryInterface $repository
    ) {}

    public function replay(string $uuid, array $overrides = []): ApiLogReplay
    {
        $log = $this->repository->find($uuid);

        if (!$log) {
            throw new \Exception("API log not found for UUID: {$uuid}");
        }

        $url = $overrides['base_url'] ?? $log->url;
        $headers = array_merge($log->headers, $overrides['headers'] ?? []);
        $method = strtolower($log->method);

        $startTime = microtime(true);

        $response = Http::withHeaders($headers)
            ->send($method, $url, [
                'query' => $log->query_params,
                'body' => $log->request_body,
            ]);

        $durationMs = (int) ((microtime(true) - $startTime) * 1000);

        return ApiLogReplay::create([
            'api_log_id' => $log->id,
            'response_status' => $response->status(),
            'response_body' => $response->body(),
            'duration_ms' => $durationMs,
        ]);
    }
}
