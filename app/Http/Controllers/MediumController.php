<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaRequest;
use App\Http\Requests\MediumRequest;
use App\Http\Resources\Medium\MediumDetails;
use App\Http\Services\MediumService;
use App\Models\Medium;

class MediumController extends Controller
{
    protected MediumService $mediumService;

    public function __construct(MediumService $mediumService)
    {
        $this->mediumService = $mediumService;
        $this->middleware('auth:sanctum')->only('storeMultiple');
    }

    public function store(MediumRequest $request)
    {
        $medium = $this->mediumService->create($request->validated());

        return new MediumDetails($medium);
    }

    public function storeMultiple(MediaRequest $request)
    {
        $media = $this->mediumService->createMultiple($request->validated());

        return MediumDetails::collection($media);
    }

    public function show(Medium $medium)
    {
        return new MediumDetails($medium);
    }
}
