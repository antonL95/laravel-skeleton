<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Connectors\ConnectorInterface;
use PDO;
use WeakMap;
use WeakReference;

final class PooledConnectionFactory extends ConnectionFactory
{
    /** @var WeakMap<Connection, bool>|null */
    private static ?WeakMap $connections = null;

    /** @var WeakReference<Connection>|null */
    private ?WeakReference $owner = null;

    public static function holdsPdo(PDO $pdo): bool
    {
        foreach (self::$connections ?? [] as $connection => $registered) {
            if ($connection->getRawPdo() === $pdo || $connection->getRawReadPdo() === $pdo) {
                return true;
            }
        }

        return false;
    }

    /** @param array<array-key, mixed> $config */
    public function make(array $config, $name = null): Connection
    {
        if (array_key_exists('read', $config) || array_key_exists('write', $config)) {
            return new ConnectionFactory($this->container)->make($config, $name);
        }

        return parent::make($config, $name);
    }

    /** @param array<array-key, mixed> $config */
    public function createConnector(array $config): ConnectorInterface
    {
        $connector = parent::createConnector($config);

        return $connector instanceof PooledPostgresConnector ? new PooledPostgresConnector($this->owner) : $connector;
    }

    /** @param array<array-key, mixed> $config */
    protected function createSingleConnection(array $config): Connection
    {
        $connection = parent::createSingleConnection($config);
        self::$connections ??= new WeakMap;
        self::$connections[$connection] = true;
        $factory = clone $this;
        $factory->owner = WeakReference::create($connection);

        $connection->setPdo($factory->createPdoResolver($config));

        return $connection;
    }
}
