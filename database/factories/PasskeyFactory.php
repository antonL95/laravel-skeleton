<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Passkey>
 */
final class PasskeyFactory extends Factory
{
    protected $model = Passkey::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'credential_id' => 'cred-'.fake()->unique()->uuid(),
            'credential' => ['aaguid' => '00000000-0000-0000-0000-000000000000'],
            'last_used_at' => null,
        ];
    }
}
