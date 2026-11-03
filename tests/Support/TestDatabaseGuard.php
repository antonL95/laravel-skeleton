<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

final class TestDatabaseGuard
{
    public static function assertSafe(?string $database): void
    {
        if ($database === null || preg_match('/_test(_test_\d+)?$/', $database) !== 1) {
            throw new RuntimeException(sprintf(
                'Refusing to run tests against the database [%s]. Its name must end in _test or _test_test_<worker>.',
                $database ?? 'none',
            ));
        }
    }
}
