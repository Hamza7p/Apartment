<?php

namespace App\Http\Controllers;

use App\Filters\ReviewFilter;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\Apartment\ReviewDetails;
use App\Http\Services\ReviewService;

class ReviewController extends Controller
{
    private ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        $this->middleware(['auth:sanctum', 'isApproved', 'setLocale']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ReviewFilter $filter)
    {
        $reviews = $this->reviewService->getAll($filter);

        return ReviewDetails::query($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
        $review = $this->reviewService->create($request->validated());

        return new ReviewDetails($review);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $review = $this->reviewService->find($id);

        return new ReviewDetails($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewRequest $request, $id)
    {
        $review = $this->reviewService->update($id, $request->validated());

        return new ReviewDetails($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->reviewService->delete($id);

        return response()->noContent();
    }
}
