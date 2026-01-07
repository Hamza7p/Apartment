<?php

namespace App\Models;

use App\Enums\Apartment\ApartmentStatus;
use App\Enums\Apartment\Governorate;
use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apartment extends BaseModel
{
    use HasFactory;

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
        'availability',
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
        'availability' => 'array',
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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
