<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class StripeProductData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $priceId,
        public readonly float $priceAmount,
        public readonly string $currency,
        public readonly string $price,
        /** @var string[] */
        public readonly array $features,
        public readonly bool $recurring,
        public readonly bool $featured,
    ) {}
}
