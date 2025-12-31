<?php

namespace Database\Factories;

use App\Enums\User\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'date_of_birth' => fake()->date(),
            'role' => fake()->randomElement(['user', 'admin']),
            'id_photo' => null,
            'personal_photo' => null,
            'phone' => fake()->unique()->numerify('9639########'),
            'password' => static::$password ??= Hash::make('password'),
            'status' => UserStatus::approved->value,

            'verified_at' => now(),
            // 'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => null,
        ]);
    }
}
