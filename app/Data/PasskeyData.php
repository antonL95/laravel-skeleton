<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Passkey;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class PasskeyData extends Data
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly ?string $authenticator,
        public readonly ?string $lastUsedAt,
        public readonly ?string $createdAt,
    ) {}

    public static function build(Passkey $passkey): self
    {
        return new self(
            uuid: $passkey->uuid,
            name: $passkey->name,
            authenticator: $passkey->authenticator,
            lastUsedAt: $passkey->last_used_at?->toIso8601String(),
            createdAt: $passkey->created_at?->toIso8601String(),
        );
    }
}
