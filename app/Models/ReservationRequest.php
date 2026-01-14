<?php

namespace App\Models;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservationRequest extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'apartment_id',
        'start_date',
        'end_date',
        'total_amount',
        'status',
        'note',
    ];

    protected $attributes = [
        'status' => ReservationStatus::PENDING->value,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => ReservationStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
