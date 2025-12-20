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
            return $apartmentId->photos()->with('medium')->get();
        }

        return ApartmentPhoto::with(['apartment', 'medium'])
            ->where('apartment_id', $apartmentId)
            ->get();
    }

    public function storeMultiplePhoto(array $data, int $apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        $mediaItems = $this->mediumService->createMultiple($data);

        $hasMain = $apartment->photos()->where('is_main', true)->exists();
        $order = $apartment->photos()->max('order') ?? 0;

        foreach ($mediaItems as $index => $medium) {
            $apartment->photos()->create([
                'medium_id' => $medium->id,
                'order' => ++$order,
                'is_main' => ! $hasMain && $index === 0,
            ]);
        }

        return $apartment->photos()->with('medium')->get();
    }

    public function setMainPhoto(int $apartmentId, int $mediumId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        // Remove main flag from all
        $apartment->photos()->update(['is_main' => false]);

        // Set new main
        $mainPhoto = $apartment->photos()
            ->where('medium_id', $mediumId)
            ->firstOrFail();

        $mainPhoto->update(['is_main' => true]);

        return $mainPhoto->load('medium');
    }

    public function getMainPhoto(int $apartmentId)
    {
        return Apartment::findOrFail($apartmentId)
            ->photos()
            ->where('is_main', true)
            ->with('medium')
            ->firstOrFail();
    }

    public function deletePhoto(int $apartmentId, int $mediumId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        $photo = $apartment->photos()
            ->where('medium_id', $mediumId)
            ->firstOrFail();

        $photo->delete();

        // Ensure a main photo exists
        if (! $apartment->photos()->where('is_main', true)->exists()) {
            $firstPhoto = $apartment->photos()->orderBy('order')->first();
            if ($firstPhoto) {
                $firstPhoto->update(['is_main' => true]);
            }
        }
    }
}
