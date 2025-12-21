<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'apartment_id',
        'start_date',
        'end_date',
        'status',
    ];

    // Condition 1: existing starts before new ends

    // Condition 2: existing ends after new starts
}
