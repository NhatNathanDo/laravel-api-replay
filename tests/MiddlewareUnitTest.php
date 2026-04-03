<?php

namespace Storage\ApiReplay\Tests;

use Illuminate\Http\Request;
use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\DTOs\ApiLogDTO;
use Storage\ApiReplay\Http\Middleware\RecordApiRequest;
use Symfony\Component\HttpFoundation\Response;
use Mockery\MockInterface;

class MiddlewareUnitTest extends TestCase
{
    /** @test */
    public function it_calls_the_repository_store_method()
    {
        $repository = $this->mock(ApiLogRepositoryInterface::class);
        $middleware = new RecordApiRequest($repository);

        $request = Request::create('/test', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['name' => 'John', 'password' => 'secret']));

        $repository->shouldReceive('store')
            ->once()
            ->withArgs(function (ApiLogDTO $dto) {
                return $dto->method === 'POST' &&
                       $dto->path === 'test' &&
                       json_decode($dto->requestBody, true)['password'] === '********';
            })
            ->andReturn('uuid-123');

        $response = $middleware->handle($request, function ($req) {
            return new Response('success', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }
}
