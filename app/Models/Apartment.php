<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Apartment extends BaseModel
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'currency',
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
        'status' => 'available',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'governorate' => 'array',
        'city' => 'array',
        'address' => 'array',
    ];

    public function photos()
    {

        return $this->belongsToMany(
            Medium::class,
            'apartment_photo',
            'apartment_id',
            'media_id'
        )->withTimestamps()->withPivot(['order', 'is_main']);

    }
}
