<?php

declare(strict_types=1);

namespace Tests\Support;

use Filament\Panel;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

final class TestingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $config = $this->app->make('config');
        $database = $config->get('database.connections.'.$config->string('database.default').'.database');

        TestDatabaseGuard::assertSafe(is_string($database) ? $database : null);

        if ($this->app->runningUnitTests()) {
            $this->app->singleton('db.factory', fn (Container $app): PooledConnectionFactory => new PooledConnectionFactory($app));
            $this->app->bind('db.connector.pgsql', PooledPostgresConnector::class);
            $this->app->bind(Panel::class, MemoizedPanel::class);
        }
    }
}
