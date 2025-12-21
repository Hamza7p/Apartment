<?php

namespace App\Models;

use App\Enums\Apartment\ApartmentStatus;
use App\Enums\Apartment\Governorate;
use App\Models\Base\BaseModel;

class Apartment extends BaseModel
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'currency',
        'rate',
        'governorate',
        'city',
        'address',
        'status',
        'number_of_room',
        'number_of_bathroom',
        'area',
        'floor',
    ];

    protected $attributes = [
        'status' => ApartmentStatus::AVAILABLE->value,
        'rate' => 0,
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'city' => 'array',
        'address' => 'array',

        'governorate' => Governorate::class,
        'status' => ApartmentStatus::class,
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function photos()
    {
        return $this->hasMany(ApartmentPhoto::class);
    }
}
