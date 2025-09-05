<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\StripeProduct;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Cashier;
use Log;

final readonly class SyncStripeProductsAction
{
    public function handle(): void
    {
        $stripe = Cashier::stripe();
        $collection = $stripe->products->all(['limit' => 100]);

        foreach ($collection->data as $stripeProductData) {
            $prices = $stripe->prices->all(['product' => $stripeProductData->id, 'limit' => 1]);

            if (!empty($prices->data)) {
                $stripePriceData = $prices->data[0];

                StripeProduct::query()->updateOrCreate(['stripe_id' => $stripeProductData->id], [
                    'name' => $stripeProductData->name,
                    'description' => $stripeProductData->description,
                    'price_id' => $stripePriceData->id,
                    'price' => $stripePriceData->unit_amount,
                    'currency' => $stripePriceData->currency,
                    'metadata' => $stripeProductData->metadata,
                    'features' => $stripeProductData->marketing_features,
                    'recurring' => $stripePriceData->type === 'recurring',
                    'active' => $stripeProductData->active,
                ]);
            } else {
                Log::warning(sprintf('Stripe product %s has no associated price.', $stripeProductData->id));
            }
        }

        Cache::forget('stripe-products');
    }
}
