<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Providers;

use Illuminate\Support\ServiceProvider;
use Storage\ApiReplay\Console\ReplayRequestCommand;
use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\Repositories\DatabaseApiLogRepository;

class ApiReplayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/api-replay.php', 'api-replay');

        $this->app->singleton(ApiLogRepositoryInterface::class, function ($app) {
            $driver = $app['config']->get('api-replay.storage_driver', 'database');

            return match ($driver) {
                'database' => new DatabaseApiLogRepository(),
                default => throw new \InvalidArgumentException("Unsupported storage driver: {$driver}"),
            };
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/api-replay.php' => config_path('api-replay.php'),
            ], 'api-replay-config');

            $this->publishes([
                __DIR__ . '/../Database/Migrations' => database_path('migrations'),
            ], 'api-replay-migrations');

            $this->commands([
                ReplayRequestCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
