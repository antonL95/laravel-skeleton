<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SocialProviderUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<SocialProviderUser>
 */
final class SocialProviderUserFactory extends Factory
{
    protected $model = SocialProviderUser::class;

    public function definition(): array
    {
        return [
            'provider_slug' => fake()->slug(),
            'provider_user_id' => fake()->word(),
            'nickname' => fake()->word(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => fake()->word(),
            'provider_data' => fake()->word(),
            'token' => Str::random(10),
            'refresh_token' => Str::random(10),
            'token_expires_at' => Carbon::now()->addDays(),
            'user_id' => User::factory(),
        ];
    }
}
