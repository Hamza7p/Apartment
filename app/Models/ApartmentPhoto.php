<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentPhoto extends Model
{
    protected $fillable = ['apartment_id', 'path'];
}
