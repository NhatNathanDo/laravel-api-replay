<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Storage\ApiReplay\Console\ReplayRequestCommand;
use Storage\ApiReplay\Http\Middleware\SimulationMiddleware;
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
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'api-replay');

        $this->app['router']->aliasMiddleware('api-replay.simulate', SimulationMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/api-replay.php' => config_path('api-replay.php'),
            ], 'api-replay-config');

            $this->publishes([
                __DIR__ . '/../Database/Migrations' => database_path('migrations'),
            ], 'api-replay-migrations');

            $this->publishes([
                __DIR__ . '/../Resources/views' => resource_path('views/vendor/api-replay'),
            ], 'api-replay-views');

            $this->commands([
                ReplayRequestCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::group([], function () {
            $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        });
    }
}
