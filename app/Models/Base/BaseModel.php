<?php

namespace App\Models\Base;

use App\Traits\TimestampRelations;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
// use Mattiverse\Userstamps\Traits\Userstamps;


abstract class BaseModel extends Model
{
    use HasTranslations,TimestampRelations;
    // use Userstamps;
    
    protected array $translatable = [];

}
