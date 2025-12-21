<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Review extends BaseModel
{
    protected $fillable = [
        'user_id',
        'apartment_id',
        'rate',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
