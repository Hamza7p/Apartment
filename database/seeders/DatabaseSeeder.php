<?php

namespace Database\Seeders;

use App\Enums\Role\RoleName;
use App\Models\Apartment;
use App\Models\ReservationRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $owner = User::factory()->create([
            'phone' => '963944068317',
            'username' => 'admin',
            'password' => Hash::make('123456789'),
            'first_name' => 'Hamza',
            'last_name' => 'At',
            'date_of_birth' => '1995-05-15',
            'role' => RoleName::admin->value,
            'status' => '1',
            'id_photo' => null,
            'personal_photo' => null,
            'verified_at' => $now,
            'updated_at' => $now,
            'created_at' => $now,
        ]);

        $user = User::factory()->create([
            'phone' => '963994848835',
            'first_name' => 'faisal',
            'password' => '123456789',

            'role' => 'user',
        ]);

        $user = User::factory()->create([
            'phone' => '963991188227',
            'password' => '123456789',
            'first_name' => 'mouhamad shaaban',
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
