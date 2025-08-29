<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\FlashMessageType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class FlashData extends Data
{
    public function __construct(
        public readonly FlashMessageType $type,
        public readonly string $title,
        public readonly ?string $description,
    ) {}
}
