<?php

namespace App\Models;

use App\Enums\Medium\MediumFor;
use App\Enums\Medium\MediumType;
use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Medium extends BaseModel
{
    protected $table = 'media';

    protected $fillable = [
        'name',
        'path',
        'extension',
        'for',
        'type',
    ];

    protected $casts = [
        'type' => MediumType::class,
        'for' => MediumFor::class,
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! Storage::disk('public')->exists($this->path)) {
                    return asset(Storage::url('_11284e3b-18a9-46f4-aa14-d2df754fc8cb.jpg'));
                }

                return asset(Storage::url($this->path));
            },
        );
    }
}
