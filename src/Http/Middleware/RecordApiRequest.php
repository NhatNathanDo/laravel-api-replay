<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\DTOs\ApiLogDTO;
use Storage\ApiReplay\Support\Masker;
use Symfony\Component\HttpFoundation\Response;

class RecordApiRequest
{
    public function __construct(
        protected ApiLogRepositoryInterface $repository
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!Config::get('api-replay.enabled', true)) {
            return $next($request);
        }
        

        if ($this->isIgnored($request)) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $durationMs = (int) ((microtime(true) - $startTime) * 1000);

        $this->logRequest($request, $response, $durationMs);

        return $response;
    }

    protected function isIgnored(Request $request): bool
    {
        $ignoredRoutes = Config::get('api-replay.ignored_routes', []);
        foreach ($ignoredRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }
        return false;
    }

    protected function logRequest(Request $request, Response $response, int $durationMs): void
    {
        $maxBodySize = Config::get('api-replay.max_body_size', 64000);
        
        $requestBody = $request->getContent();
        if (strlen((string) $requestBody) > $maxBodySize) {
            $requestBody = substr((string) $requestBody, 0, $maxBodySize) . '... [TRUNCATED]';
        }

        $responseBody = null;
        if (Config::get('api-replay.log_response', true)) {
            $responseBody = $response->getContent();
            if (strlen((string) $responseBody) > $maxBodySize) {
                $responseBody = substr((string) $responseBody, 0, $maxBodySize) . '... [TRUNCATED]';
            }
        }

        $sensitiveHeaders = Config::get('api-replay.sensitive_headers', ['Authorization', 'Cookie']);
        $sensitiveFields = Config::get('api-replay.sensitive_fields', ['password', 'password_confirmation', 'token']);

        $headers = Masker::maskHeaders($request->headers->all(), $sensitiveHeaders);
        
        // Only mask if content type is JSON
        if (str_contains((string) $request->header('Content-Type'), 'application/json')) {
            $data = json_decode((string) $request->getContent(), true);
            if (is_array($data)) {
                $requestBody = json_encode(Masker::maskData($data, $sensitiveFields));
            }
        }

        $logDto = new ApiLogDTO(
            method: $request->method(),
            url: $request->fullUrl(),
            path: $request->path(),
            headers: $headers,
            queryParams: $request->query(),
            requestBody: (string) $requestBody,
            responseStatus: $response->getStatusCode(),
            responseBody: (string) $responseBody,
            durationMs: $durationMs,
            ip: $request->ip(),
            userId: $request->user()?->id,
        );

        if (Config::get('api-replay.queue_enabled', false)) {
            // TODO: Dispatch job if queue is enabled
            // For now, store directly as backup
            $this->repository->store($logDto);
        } else {
            $this->repository->store($logDto);
        }
    }
}
