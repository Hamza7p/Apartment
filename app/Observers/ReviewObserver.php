<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->updateApartmentRate($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $this->updateApartmentRate($review);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->updateApartmentRate($review);
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }

    protected function updateApartmentRate(Review $review)
    {
        $apartment = $review->apartment;

        if (! $apartment) {
            return;
        }

        $newRate = $apartment->reviews()->avg('rate');
        if ($apartment->rate != $newRate) {
            $apartment->rate = $apartment->reviews()->avg('rate');
            $apartment->saveQuietly();
        }
    }
}
