<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class ApartmentPhoto extends BaseModel
{
    protected $fillable = [
        'apartment_id',
        'medai_id',
        'order',
        'is_main',
    ];
}
