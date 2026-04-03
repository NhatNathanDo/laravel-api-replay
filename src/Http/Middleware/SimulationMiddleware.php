<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SimulationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $dryRunHeader = Config::get('api-replay.dry_run_header', 'X-Api-Replay-Dry-Run');
        $isDryRun = $request->hasHeader($dryRunHeader);
        $rollbackEnabled = Config::get('api-replay.enable_db_rollback', true);

        if ($isDryRun && $rollbackEnabled) {
            DB::beginTransaction();
            
            try {
                $response = $next($request);
                
                // Content is generated, now rollback before finishing
                DB::rollBack();
                
                return $response;
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return $next($request);
    }
}
