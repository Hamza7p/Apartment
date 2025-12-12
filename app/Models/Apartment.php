<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'user_id',
        'number_of_room',
        'address',
        'services',
        'description',
        'is_available',
        'price',
    ];

    public function photos()
    {
        return $this->hasMany(ApartmentPhoto::class);
    }
}
