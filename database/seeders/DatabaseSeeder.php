<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ReservationRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $owner = User::factory()->create([
            'phone' => '963944068317',
            'password' => '123456789',
            'first_name' => 'Hamza',
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'phone' => '963994848835',
            'first_name' => 'faisal',
            'password' => '123456789',

            'role' => 'user',
        ]);

        $apartment = Apartment::factory()
            ->available()
            ->create(['user_id' => $owner->id]);

        // ReservationRequest::factory()->create([
        //     'user_id' => $user->id,
        //     'apartment_id' => $apartment->id,
        // ]);

        // $this->call([
        //     UserSeeder::class,
        //     ApartmentSeeder::class,
        // ]);
    }
}
