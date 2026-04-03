<?php

namespace Storage\ApiReplay\Tests;

use Illuminate\Support\Facades\Http;
use Storage\ApiReplay\Models\ApiLog;
use Storage\ApiReplay\Services\ReplayService;

class ReplayServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is not available.');
        }
    }

    /** @test */
    public function it_replays_a_recorded_request()
    {
        $log = ApiLog::create([
            'method' => 'POST',
            'url' => 'https://api.example.com/data',
            'path' => 'data',
            'headers' => ['Content-Type' => ['application/json']],
            'query_params' => ['q' => 'test'],
            'request_body' => json_encode(['foo' => 'bar']),
            'response_status' => 200,
            'duration_ms' => 100,
        ]);

        Http::fake([
            'https://api.example.com/data*' => Http::response(['success' => true], 201),
        ]);

        $service = app(ReplayService::class);
        $replay = $service->replay($log->id);

        $this->assertEquals(201, $replay->response_status);
        $this->assertStringContainsString('success', $replay->response_body);
        $this->assertDatabaseHas('api_log_replays', [
            'api_log_id' => $log->id,
            'response_status' => 201,
        ]);

        Http::assertSent(function ($request) use ($log) {
            return $request->url() === $log->url . '?q=test' &&
                   $request->method() === 'POST' &&
                   $request->body() === json_encode(['foo' => 'bar']);
        });
    }
}
