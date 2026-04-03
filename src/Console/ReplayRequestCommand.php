<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Console;

use Illuminate\Console\Command;
use Storage\ApiReplay\Services\ReplayService;

class ReplayRequestCommand extends Command
{
    protected $signature = 'replay:request {id : The UUID of the request to replay} 
                            {--override-header=* : Headers to override in key:value format}
                            {--base-url= : Override the base URL}
                            {--no-auth : Skip recording this request?}';

    protected $description = 'Replay a recorded API request';

    public function handle(ReplayService $replayService): int
    {
        $id = $this->argument('id');
        $baseUrl = $this->option('base-url');
        $overridesHeaders = $this->option('override-header');

        $headers = [];
        foreach ($overridesHeaders as $header) {
            [$key, $value] = explode(':', $header, 2);
            $headers[$key] = $value;
        }

        $this->info("Replaying request {$id}...");

        try {
            $replay = $replayService->replay((string) $id, [
                'base_url' => $baseUrl,
                'headers' => $headers,
            ]);

            $this->table(['Status', 'Duration (ms)', 'Created At'], [
                [$replay->response_status, $replay->duration_ms, $replay->created_at],
            ]);

            if ($this->confirm('View response body?')) {
                $this->line((string) $replay->response_body);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
