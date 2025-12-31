<?php

namespace Database\Factories;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Apartment;
use App\Models\ReservationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReservationRequest>
 */
class ReservationRequestFactory extends Factory
{
    protected $model = ReservationRequest::class;

    public function definition(): array
    {
        $start = now()->addDays(fake()->numberBetween(1, 10));
        $end = (clone $start)->addDays(fake()->numberBetween(1, 14));

        return [
            'user_id' => User::factory(),          // requester
            'apartment_id' => Apartment::factory(), // apartment
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'status' => ReservationStatus::PENDING->value,
            'note' => fake()->optional()->sentence(),
        ];
    }

    /* ===================== STATES ===================== */

    public function pending(): static
    {
        return $this->state([
            'status' => ReservationStatus::PENDING->value,
        ]);
    }

    public function accepted(): static
    {
        return $this->state([
            'status' => ReservationStatus::ACCEPTED->value,
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => ReservationStatus::REJECTED->value,
        ]);
    }
}
