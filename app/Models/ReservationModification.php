<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservationModification extends BaseModel
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
