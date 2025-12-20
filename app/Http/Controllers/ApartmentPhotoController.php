<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaRequest;
use App\Http\Resources\Apartment\ApartmentPhotoDetails;
use App\Http\Services\ApartmentPhotoService;

class ApartmentPhotoController extends Controller
{
    private ApartmentPhotoService $apartmentPhotoService;

    public function __construct(ApartmentPhotoService $apartmentPhotoService)
    {
        $this->apartmentPhotoService = $apartmentPhotoService;
        $this->middleware(['setLocale', 'auth:sanctum', 'isApproved']);
    }

    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Display a listing of the resource
     */
    public function index($apartmentId)
    {
        $photos = $this->apartmentPhotoService->getPhotos($apartmentId);

        return ApartmentPhotoDetails::collection($photos);
    }

    public function store(MediaRequest $request, $apartment_id)
    {
        $photos = $this->apartmentPhotoService->storeMultiplePhoto($request->validated(), $apartment_id);

        return ApartmentPhotoDetails::collection($photos);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($apartmentId, $mediaId)
    {
        $this->apartmentPhotoService->deletePhoto($apartmentId, $mediaId);

        return response()->noContent();
    }

    public function setMainPhoto($apartmentId, $mediaId)
    {
        $mainPhoto = $this->apartmentPhotoService->setMainPhoto($apartmentId, $mediaId);

        return new ApartmentPhotoDetails($mainPhoto);

    }

    public function getMainPhoto($apartmentId)
    {
        $mainPhoto = $this->apartmentPhotoService->getMainPhoto($apartmentId);

        return new ApartmentPhotoDetails($mainPhoto);
    }
}
