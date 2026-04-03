<?php

namespace Storage\ApiReplay\Tests;

use Illuminate\Support\Facades\Route;
use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\DTOs\ApiLogDTO;
use Storage\ApiReplay\Http\Middleware\RecordApiRequest;
use Mockery\MockInterface;

class MiddlewareTest extends TestCase
{
    /** @var MockInterface */
    protected $repository;

    protected function defineRoutes($router)
    {
        $router->any('/test-api', function () {
            return response()->json(['message' => 'success']);
        })->middleware(RecordApiRequest::class);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->mock(ApiLogRepositoryInterface::class);
    }

    /** @test */
    public function it_records_an_incoming_request()
    {
        $this->repository->shouldReceive('store')
            ->once()
            ->withArgs(function (ApiLogDTO $dto) {
                return $dto->method === 'POST' &&
                       str_contains($dto->url, '/test-api') &&
                       $dto->headers['Authorization'] === ['********'] &&
                       json_decode($dto->requestBody, true)['password'] === '********';
            })
            ->andReturn('test-uuid');

        $this->postJson('/test-api', [
            'name' => 'John Doe',
            'password' => 'secret123',
        ], [
            'Authorization' => 'Bearer token123',
        ]);
    }
}
