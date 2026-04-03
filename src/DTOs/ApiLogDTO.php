<?php

declare(strict_types=1);

namespace Storage\ApiReplay\DTOs;

final class ApiLogDTO
{
    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly string $path,
        public readonly array $headers,
        public readonly array $queryParams,
        public readonly ?string $requestBody,
        public readonly int $responseStatus,
        public readonly ?string $responseBody,
        public readonly int $durationMs,
        public readonly ?string $ip = null,
        public readonly string|int|null $userId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            method: $data['method'],
            url: $data['url'],
            path: $data['path'],
            headers: $data['headers'],
            queryParams: $data['query_params'],
            requestBody: $data['request_body'] ?? null,
            responseStatus: $data['response_status'],
            responseBody: $data['response_body'] ?? null,
            durationMs: $data['duration_ms'],
            ip: $data['ip'] ?? null,
            userId: $data['user_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'url' => $this->url,
            'path' => $this->path,
            'headers' => $this->headers,
            'query_params' => $this->queryParams,
            'request_body' => $this->requestBody,
            'response_status' => $this->responseStatus,
            'response_body' => $this->responseBody,
            'duration_ms' => $this->durationMs,
            'ip' => $this->ip,
            'user_id' => $this->userId,
        ];
    }
}
