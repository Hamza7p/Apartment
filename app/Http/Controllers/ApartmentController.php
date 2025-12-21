<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApartmenRequest;
use App\Http\Resources\Apartment\ApartmentDetails;
use App\Http\Services\ApartmentService;

class ApartmentController extends Controller
{
    private ApartmentService $apartmentService;

    public function __construct(ApartmentService $apartmentService)
    {
        $this->apartmentService = $apartmentService;
        $this->middleware(['setLocale', 'auth:sanctum', 'isApproved']);
        // we need to add midellwire here
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApartmenRequest $request)
    {
        $apartment = $this->apartmentService->create($request->validated());

        return new ApartmentDetails($apartment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $apartment = $this->apartmentService->find($id);

        return new ApartmentDetails($apartment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, ApartmenRequest $request)
    {
        $apartment = $this->apartmentService->update($id, $request->validated());

        return new ApartmentDetails($apartment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->apartmentService->delete($id);

        return response()->noContent();
    }
}
