<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationModification extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'type',
        'old_value',
        'new_value',
        'requested_by',
        'status',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
