<?php

declare(strict_types=1);

use Tests\Support\TestDatabaseGuard;

it('allows test database names', function (string $database): void {
    TestDatabaseGuard::assertSafe($database);
})->with(['laravel_test', 'laravel_test_test_1', 'laravel_test_test_4'])->throwsNoExceptions();

it('refuses every other database name', function (?string $database): void {
    expect(fn () => TestDatabaseGuard::assertSafe($database))->toThrow(RuntimeException::class);
})->with([
    'dev database' => 'laravel',
    'test in the middle' => 'my_test_app',
    'test prefix' => 'test_laravel',
    'testing' => 'testing',
    'empty' => '',
    'missing' => null,
]);
