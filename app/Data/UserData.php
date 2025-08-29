<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $isSubscribed,
        public readonly string $initials,
        public readonly ?CarbonImmutable $emailVerifiedAt,
    ) {}

    public static function build(User $user): self
    {
        return new self(
            $user->id,
            $user->name,
            $user->email,
            false,
            $user->initials(),
            $user->email_verified_at,
        );
    }
}
