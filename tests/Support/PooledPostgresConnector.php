<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\PostgresConnector;
use PDO;
use PDOException;
use WeakReference;

final class PooledPostgresConnector extends PostgresConnector
{
    /** @var array<string, array{pdo: PDO, attributes: array<int, mixed>}> */
    private static array $connections = [];

    /** @param WeakReference<Connection>|null $owner */
    public function __construct(private readonly ?WeakReference $owner = null) {}

    /** @param array<array-key, mixed> $config */
    public function connect(array $config): PDO
    {
        $database = $config['database'] ?? null;

        $physicalDatabase = $config['connect_via_database'] ?? $database;

        if (!$this->owner instanceof WeakReference || !$this->owner->get() instanceof Connection || array_key_exists('read', $config) || array_key_exists('write', $config) || !is_string($physicalDatabase) || !str_starts_with($physicalDatabase, 'laravel_test') || !app()->runningUnitTests() || config('database.default') !== 'pgsql' || ($config['name'] ?? null) !== 'pgsql' || !is_string($database) || !str_starts_with($database, 'laravel_test')) {
            return parent::connect($config);
        }

        $key = hash('xxh128', serialize($config));
        if (isset(self::$connections[$key])) {
            $entry = self::$connections[$key];
            $pdo = $entry['pdo'];

            if (PooledConnectionFactory::holdsPdo($pdo)) {
                return parent::connect($config);
            }

            try {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                foreach ($entry['attributes'] as $attribute => $value) {
                    $pdo->setAttribute($attribute, $value);
                }

                $pdo->exec('DISCARD ALL');
                $this->configureIsolationLevel($pdo, $config);
                $this->configureTimezone($pdo, $config);
                $this->configureSearchPath($pdo, $config);
                $this->configureSynchronousCommit($pdo, $config);

                return $pdo;
            } catch (PDOException) {
                unset(self::$connections[$key]);
            }
        }

        $pdo = parent::connect($config);
        $attributes = [];
        foreach ([PDO::ATTR_ERRMODE, PDO::ATTR_CASE, PDO::ATTR_ORACLE_NULLS, PDO::ATTR_STRINGIFY_FETCHES, PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ATTR_EMULATE_PREPARES] as $attribute) {
            $attributes[$attribute] = $pdo->getAttribute($attribute);
        }

        self::$connections[$key] = ['pdo' => $pdo, 'attributes' => $attributes];

        return $pdo;
    }
}
