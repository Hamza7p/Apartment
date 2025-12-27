<?php

namespace Database\Seeders;

use App\Enums\Apartment\ApartmentStatus;
use App\Enums\Apartment\Governorate;
use App\Models\Apartment;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        Apartment::create([
            'user_id' => 2,

            'title' => [
                'en' => 'Apartment',
                'ar' => 'شقة',
            ],

            'description' => [
                'en' => 'New apartment',
                'ar' => 'شقة جديدة',
            ],

            'price' => 300.00,
            'currency' => '$',

            'governorate' => Governorate::DAMASCUS->value,

            'city' => [
                'en' => 'al_taibah',
                'ar' => 'الطيبة',
            ],

            'address' => [
                'en' => 'Damascus',
                'ar' => 'دمشق',
            ],

            'status' => ApartmentStatus::AVAILABLE->value,
            'available_at' => '2025-12-25',

            'number_of_room' => 4,
            'number_of_bathroom' => 2,
            'area' => 160,
            'floor' => 1,
        ]);
    }
}
