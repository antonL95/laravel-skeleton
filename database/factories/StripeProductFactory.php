<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StripeProduct;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StripeProduct>
 */
final class StripeProductFactory extends Factory
{
    protected $model = StripeProduct::class;

    public function definition(): array
    {
        return [
            'stripe_id' => fake()->word(),
            'name' => fake()->name(),
            'description' => fake()->text(),
            'price_id' => fake()->word(),
            'price' => fake()->randomNumber(),
            'currency' => fake()->word(),
            'metadata' => fake()->words(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
