<?php

namespace App\Http\Services;

use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\ApartmentPhoto;

class ApartmentPhotoService // extends CrudService
{
    protected MediumService $mediumService;

    public function __construct(MediumService $mediumService)
    {
        $this->mediumService = $mediumService;
    }

    protected function getModelClass(): string
    {
        return apartmentPhoto::class;
    }

    public function getPhotos($apartmentId)
    {
        if ($apartmentId instanceof Apartment) {
            return $apartmentId->photos;
        }

        $apartment = ApartmentPhoto::with(['medium', 'apartment'])->where('apartment_id', $apartmentId)->get();

        return $apartment;
    }

    public function storeMultiplePhoto(array $data, $apartment_id)
    {
        $apartment = Apartment::findOrFail($apartment_id);

        $mediaItems = $this->mediumService->createMultiple($data);

        $hasMain = $apartment->photos()->wherePivot('is_main', true)->exists();
        $lastOrder = $apartment->photos()->max('apartment_photos.order');
        $startOrder = $lastOrder ?? 0;
        $attachData = [];
        foreach ($mediaItems as $index => $medium) {
            $attachData[$medium->id] = [
                'order' => $startOrder + 1,
                'is_main' => ! $hasMain && $index === 0,
            ];
        }
        $apartment->photos()->attach($attachData);

        return $apartment->photos()->get();
    }

    public function setMainPhoto($apartmentId, $mediaId)
    {
        $apartment = Apartment::findOrFail($apartmentId);
        // $mainPhoto = $apartment->photos()->where('media_id', $mediaId)->firstOrFail();

        $apartment->photos()->update(['apartment_photos.is_main' => 0]);

        $apartment->photos()->updateExistingPivot(
            $mediaId,
            ['is_main' => true]
        );

        return $apartment->photos()->wherePivot('is_main', true)->firstOrFail();

    }

    public function getMainPhoto($apartmentId)
    {
        $mainPhoto = Apartment::findOrFail($apartmentId)->photos()->wherePivot('is_main', 1)->firstOrFail();

        return $mainPhoto;
    }

    public function deletePhoto($apartmentId, $mediaId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        $apartment->photos()->detach($mediaId);

        $hasMain = $apartment->photos()
            ->wherePivot('is_main', true)
            ->exists();

        if (! $hasMain) {
            $firstPhoto = $apartment->photos()
                ->orderBy('apartment_photos.order')
                ->first();

            if ($firstPhoto) {
                $this->setMainPhoto($apartmentId, $firstPhoto->id);
            }
        }
    }
}
