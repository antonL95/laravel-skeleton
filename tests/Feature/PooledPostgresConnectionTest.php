<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\DB;
use Tests\Support\PostgresSessionState;

function pooledConnectionScalar(PDO $pdo, string $sql): int|string|null
{
    $statement = $pdo->query($sql);

    throw_if($statement === false, RuntimeException::class, 'Unable to query the PostgreSQL session.');

    $value = $statement->fetchColumn();

    throw_unless(is_int($value) || is_string($value) || $value === null, RuntimeException::class, 'Expected a scalar PostgreSQL session value.');

    return $value;
}

it('leaves rolled back data and dirty session state for the next test', function (): PostgresSessionState {
    $pdo = DB::connection()->getPdo();
    $pid = (int) pooledConnectionScalar($pdo, 'select pg_backend_pid()');
    $settings = [];
    $attributes = [];

    foreach (['TimeZone', 'search_path', 'application_name', 'statement_timeout', 'client_encoding', 'default_transaction_isolation', 'synchronous_commit'] as $setting) {
        $settings[$setting] = pooledConnectionScalar($pdo, 'show '.$setting);
    }

    foreach ([PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ATTR_EMULATE_PREPARES] as $attribute) {
        $attributes[$attribute] = $pdo->getAttribute($attribute);
    }

    User::factory()->create(['email' => 'pooled-connection@example.test']);
    $this->assertDatabaseHas('users', ['email' => 'pooled-connection@example.test']);

    $this->beforeApplicationDestroyed(function () use ($pdo): void {
        expect($pdo->inTransaction())->toBeFalse();
        $pdo->exec("set timezone = 'Pacific/Honolulu'; set search_path to pg_catalog, public; set application_name = 'dirty-session'; set statement_timeout = '12s'; set test_pool.sentinel = 'dirty'; set client_encoding = 'LATIN1'; set session characteristics as transaction isolation level serializable; set synchronous_commit = 'off'");
        $pdo->exec('create temporary table pooled_connection_temp (id bigint)');
        $pdo->exec('prepare pooled_connection_statement as select 1');
        pooledConnectionScalar($pdo, 'select pg_advisory_lock(814731)');
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    });

    return new PostgresSessionState($pid, $settings, $attributes);
});

it('reuses the backend without data or session state from the previous test', function (PostgresSessionState $state): void {
    $pdo = DB::connection()->getPdo();

    expect((int) pooledConnectionScalar($pdo, 'select pg_backend_pid()'))->toBe($state->pid);
    $this->assertDatabaseMissing('users', ['email' => 'pooled-connection@example.test']);

    foreach ($state->settings as $setting => $expected) {
        expect(pooledConnectionScalar($pdo, 'show '.$setting))->toBe($expected);
    }

    foreach ($state->attributes as $attribute => $expected) {
        expect($pdo->getAttribute($attribute))->toBe($expected);
    }

    expect((int) pooledConnectionScalar($pdo, "select count(*) from pg_prepared_statements where name = 'pooled_connection_statement'"))->toBe(0)
        ->and(pooledConnectionScalar($pdo, "select to_regclass('pg_temp.pooled_connection_temp')"))->toBeNull()
        ->and((int) pooledConnectionScalar($pdo, "select count(*) from pg_locks where pid = pg_backend_pid() and locktype = 'advisory'"))->toBe(0)
        ->and(pooledConnectionScalar($pdo, "select current_setting('test_pool.sentinel', true)"))->toBeIn([null, '']);

    config(['database.connections.secondary' => config('database.connections.pgsql')]);

    try {
        $other = DB::connection('secondary')->getPdo();

        expect((int) pooledConnectionScalar($other, 'select pg_backend_pid()'))->not->toBe($state->pid);
    } finally {
        DB::purge('secondary');
    }
})->depends('it leaves rolled back data and dirty session state for the next test');

it('restores configured session settings and rolls back residual transactions', function (): void {
    $config = array_replace(config()->array('database.connections.pgsql'), [
        'name' => 'pgsql',
        'application_name' => 'configured-session',
        'isolation_level' => 'repeatable read',
        'timezone' => 'Europe/Prague',
        'search_path' => 'public',
        'synchronous_commit' => 'off',
    ]);
    /** @var ConnectionFactory $factory */
    $factory = resolve('db.factory');
    $connection = $factory->make($config, 'pgsql');
    $pdo = $connection->getPdo();
    $pid = (int) pooledConnectionScalar($pdo, 'select pg_backend_pid()');
    $pdo->exec("set timezone = 'Pacific/Honolulu'; set search_path to pg_catalog; set synchronous_commit = 'on'; set session characteristics as transaction isolation level serializable");
    $pdo->beginTransaction();
    $pdo->exec('create temporary table residual_transaction_temp (id bigint)');

    $connection->disconnect();
    $reused = $factory->make($config, 'pgsql')->getPdo();

    expect((int) pooledConnectionScalar($reused, 'select pg_backend_pid()'))->toBe($pid)
        ->and($reused->inTransaction())->toBeFalse()
        ->and(pooledConnectionScalar($reused, 'show timezone'))->toBe('Europe/Prague')
        ->and(pooledConnectionScalar($reused, 'show search_path'))->toBe('public')
        ->and(pooledConnectionScalar($reused, 'show synchronous_commit'))->toBe('off')
        ->and(pooledConnectionScalar($reused, 'show default_transaction_isolation'))->toBe('repeatable read')
        ->and(pooledConnectionScalar($reused, "select to_regclass('pg_temp.residual_transaction_temp')"))->toBeNull();
});

it('reconnects when the pooled backend has closed', function (): void {
    $config = array_replace(config()->array('database.connections.pgsql'), ['name' => 'pgsql', 'application_name' => 'terminated-session']);
    /** @var ConnectionFactory $factory */
    $factory = resolve('db.factory');
    $connection = $factory->make($config, 'pgsql');
    $pdo = $connection->getPdo();
    $pid = (int) pooledConnectionScalar($pdo, 'select pg_backend_pid()');
    DB::selectOne('select pg_terminate_backend(?)', [$pid]);

    $connection->disconnect();
    $replacement = $factory->make($config, 'pgsql')->getPdo();

    expect((int) pooledConnectionScalar($replacement, 'select pg_backend_pid()'))->not->toBe($pid);
});

it('keeps an explicit write connection separate from the active primary transaction', function (): void {
    $primary = DB::connection();
    $pdo = $primary->getPdo();
    $pid = (int) pooledConnectionScalar($pdo, 'select pg_backend_pid()');
    $primary->beginTransaction();

    try {
        $alias = DB::connection('pgsql::write')->getPdo();

        expect($pdo->inTransaction())->toBeTrue()
            ->and((int) pooledConnectionScalar($alias, 'select pg_backend_pid()'))->not->toBe($pid)
            ->and($primary->transactionLevel())->toBe(2);
    } finally {
        if ($pdo->inTransaction()) {
            $primary->rollBack();
        }

        DB::purge('pgsql::write');
    }
});

it('reuses a purged primary connection with clean session state', function (): void {
    $primary = DB::connection();
    $pdo = $primary->getPdo();
    $pid = (int) pooledConnectionScalar($pdo, 'select pg_backend_pid()');
    $timezone = pooledConnectionScalar($pdo, 'show timezone');
    $pdo->exec("set timezone = 'Pacific/Honolulu'; create temporary table purged_connection_temp (id bigint)");

    DB::purge();
    $connection = DB::connection();
    $reused = $connection->getPdo();

    expect($connection)->not->toBe($primary)
        ->and($reused)->toBe($pdo)
        ->and((int) pooledConnectionScalar($reused, 'select pg_backend_pid()'))->toBe($pid)
        ->and($reused->inTransaction())->toBeFalse()
        ->and(pooledConnectionScalar($reused, 'show timezone'))->toBe($timezone)
        ->and(pooledConnectionScalar($reused, "select to_regclass('pg_temp.purged_connection_temp')"))->toBeNull();

    $connection->beginTransaction();
});

it('keeps a reconnected write alias leased while its transaction is active', function (): void {
    DB::connection('pgsql::write')->getPdo();
    DB::disconnect();
    $alias = DB::reconnect('pgsql::write');
    gc_collect_cycles();
    $pdo = $alias->getPdo();
    $pid = (int) pooledConnectionScalar($pdo, 'select pg_backend_pid()');
    $alias->beginTransaction();

    try {
        DB::purge();
        $primary = DB::connection()->getPdo();

        expect($pdo->inTransaction())->toBeTrue()
            ->and((int) pooledConnectionScalar($primary, 'select pg_backend_pid()'))->not->toBe($pid);
    } finally {
        if ($pdo->inTransaction()) {
            $alias->rollBack();
        }

        DB::purge('pgsql::write');
        DB::connection()->beginTransaction();
    }
});
