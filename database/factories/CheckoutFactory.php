<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Checkout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkout>
 */
final class CheckoutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Checkout>
     */
    protected $model = Checkout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'price_id' => fake()->uuid(),
            'checkout_session_id' => fake()->uuid(),
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the checkout is completed.
     */
    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the checkout is pending.
     */
    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the checkout is cancelled.
     */
    public function cancelled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
