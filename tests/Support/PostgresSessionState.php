<?php

declare(strict_types=1);

namespace Tests\Support;

final readonly class PostgresSessionState
{
    /**
     * @param  array<string, int|string|null>  $settings
     * @param  array<int, mixed>  $attributes
     */
    public function __construct(public int $pid, public array $settings, public array $attributes) {}
}
