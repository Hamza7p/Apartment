<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileOtp extends BaseModel
{
    protected $table = 'mobile_otp';
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'verified',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }

}
