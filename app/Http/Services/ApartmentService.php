<?php

namespace App\Http\Services;

use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\Base\BaseModel;
use Illuminate\Support\Facades\Auth;

class ApartmentService extends CrudService
{
    protected function getModelClass(): string
    {
        return Apartment::class;
    }

    public function create(array $data): BaseModel
    {
        $user_id = Auth::user()->id;
        $data['user_id'] = $user_id;

        return parent::create($data);
    }
}
