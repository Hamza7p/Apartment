<?php

namespace App\Models;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Base\BaseModel;

class Reservation extends BaseModel
{
    protected $fillable = [
        'reservation_request_id',
        'user_id',
        'apartment_id',
        'start_date',
        'end_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => ReservationStatus::class,
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Condition 1: existing starts before new ends

    // Condition 2: existing ends after new starts
}
