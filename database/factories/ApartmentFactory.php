<?php

namespace Database\Factories;

use App\Enums\Apartment\ApartmentStatus;
use App\Enums\Apartment\Governorate;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Apartment>
 */
class ApartmentFactory extends Factory
{
    protected $model = Apartment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // owner
            'title' => [
                'en' => fake()->sentence(3),
                'ar' => 'شقة تجريبية',
            ],
            'description' => [
                'en' => fake()->paragraph(),
                'ar' => 'وصف الشقة',
            ],
            'price' => fake()->numberBetween(100, 1000),
            'currency' => '$',
            'rate' => fake()->randomFloat(1, 0, 5),
            'governorate' => fake()->randomElement([
                Governorate::DAMASCUS->value,
                Governorate::ALEPPO->value,
            ]),
            'city' => [
                'en' => fake()->city(),
                'ar' => 'دمشق',
            ],
            'address' => [
                'en' => fake()->address(),
                'ar' => 'عنوان تجريبي',
            ],
            'status' => ApartmentStatus::AVAILABLE->value,
            'availability' => null,
            'number_of_room' => fake()->numberBetween(1, 5),
            'number_of_bathroom' => fake()->numberBetween(1, 3),
            'area' => fake()->numberBetween(50, 250),
            'floor' => fake()->numberBetween(0, 10),
        ];
    }

    /* ===================== STATES ===================== */

    public function available(): static
    {
        return $this->state([
            'status' => ApartmentStatus::AVAILABLE->value,
            'availability' => now(),
        ]);
    }

    public function underMaintenance(): static
    {
        return $this->state([
            'status' => ApartmentStatus::UNDER_MAINTENANCE->value,
            'availability' => null,
        ]);
    }
}
