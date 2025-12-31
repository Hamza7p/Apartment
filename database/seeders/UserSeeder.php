<?php

namespace Database\Seeders;

use App\Enums\Role\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        User::create([
            'phone' => '963944068317',
            'username' => 'admin',
            'password' => Hash::make('password'),
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

        User::create([
            'phone' => '963994848835',
            'username' => 'admin2',
            'password' => Hash::make('password'),
            'first_name' => 'faiasl',
            'last_name' => 'hendiah',
            'date_of_birth' => '2005-4-6',
            'role' => RoleName::admin->value,
            'status' => '1',
            'id_photo' => null,
            'personal_photo' => null,
            'verified_at' => $now,
            'updated_at' => $now,
            'created_at' => $now,
        ]);

        // $owner = User::factory()->create([
        //     'phone' => '963944068317',
        // ]);

        // $user = User::factory()->create([
        //     'phone' => '963994848835',
        // ]);

    }
}
