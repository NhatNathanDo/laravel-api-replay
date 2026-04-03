<?php

namespace Storage\ApiReplay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Storage\ApiReplay\Providers\ApiReplayServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ApiReplayServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        if (extension_loaded('pdo_sqlite')) {
            $this->loadMigrationsFrom(__DIR__ . '/../src/Database/Migrations');
        }
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
