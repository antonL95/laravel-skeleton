<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\StripeProductData;
use App\Models\StripeProduct;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Number;

final readonly class FetchStripeProducts
{
    /**
     * @return StripeProductData[]
     */
    public function handle(): array
    {
        if (Cache::has('stripe-products')) {
            /** @var StripeProductData[] $stripeProduct */
            $stripeProduct = (array) Cache::get('stripe-products');

            return $stripeProduct;
        }

        $stripeProducts = StripeProduct::query()->where('active', true)->get();

        $result = [];
        foreach ($stripeProducts as $stripeProduct) {
            /** @var string[] $features */
            $features = Arr::flatten((array) $stripeProduct->features);

            $result[] = new StripeProductData(
                (string) $stripeProduct->id,
                (string) $stripeProduct->name,
                (string) $stripeProduct->description,
                (string) $stripeProduct->price_id,
                $stripeProduct->price / 100,
                (string) $stripeProduct->currency,
                (string) Number::currency($stripeProduct->price / 100, (string) $stripeProduct->currency, locale: Config::string('app.locale')),
                $features,
                $stripeProduct->recurring,
                isset($stripeProduct->metadata['featured']) && $stripeProduct->metadata['featured'] === 'true',
            );
        }

        usort($result, static fn (StripeProductData $a, StripeProductData $b): int => $a->name <=> $b->name);

        Cache::forever('stripe-products', $result);

        return $result;
    }
}
