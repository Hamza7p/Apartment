<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class ApartmentPhoto extends BaseModel
{
    protected $fillable = [
        'apartment_id',
        'medium_id',
        'order',
        'is_main',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function medium()
    {
        return $this->belongsTo(Medium::class);
    }
}
