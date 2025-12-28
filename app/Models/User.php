<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Role\RoleName;
use App\Enums\User\UserStatus;
use App\Models\Base\BaseModel;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Collection\Collection;

class User extends BaseModel implements Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use AuthenticatableTrait, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phone',
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
        'id_photo',
        'personal_photo',
        'username',
        'role',
        'status',
        'fcm_token',
        // 'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'role' => RoleName::class,
        ];
    }

    public function idPhoto(): BelongsTo
    {
        return $this->belongsTo(Medium::class, 'id_photo');
    }

    public function personalPhoto(): BelongsTo
    {
        return $this->belongsTo(Medium::class, 'personal_photo');
    }

    public function isAdmin(): bool
    {
        return $this->role->value === RoleName::admin->value;
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', RoleName::admin->value);
    }

    public function favoriteApartments()
    {
        return $this->belongsToMany(Apartment::class, 'favorites');
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
