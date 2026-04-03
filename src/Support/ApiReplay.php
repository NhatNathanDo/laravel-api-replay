<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class ApiReplay
{
    /**
     * Check if the current request is a replay.
     */
    public static function isReplay(): bool
    {
        return Request::hasHeader('X-Api-Replay-Origin');
    }

    /**
     * Check if the current request is a dry-run (simulation).
     */
    public static function isDryRun(): bool
    {
        $header = Config::get('api-replay.dry_run_header', 'X-Api-Replay-Dry-Run');
        return Request::hasHeader($header);
    }

    /**
     * Helper to skip logic if replaying/simulating.
     */
    public static function shouldSkip(bool $onlyOnDryRun = false): bool
    {
        if ($onlyOnDryRun) {
            return self::isDryRun();
        }
        return self::isReplay();
    }
}
