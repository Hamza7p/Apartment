<?php

namespace App\Http\Services;

use App\Http\Services\Base\CrudService;
use App\Models\Apartment;
use App\Models\ApartmentPhoto;
use Illuminate\Support\Facades\DB;

class ApartmentPhotoService // extends CrudService
{
    protected MediumService $mediumService;

    public function __construct(MediumService $mediumService)
    {
        $this->mediumService = $mediumService;
    }

    protected function getModelClass(): string
    {
        return ApartmentPhoto::class;
    }

    public function getPhotos(int|Apartment $apartmentId)
    {
        $apartment = $apartmentId instanceof Apartment
            ? $apartmentId
            : Apartment::findOrFail($apartmentId);

        return $apartment->photos()->with('medium')->orderBy('order')->get();
    }

    public function storeMultiplePhoto(array $data, int $apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        return DB::transaction(function () use ($apartment, $data) {
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

            return $apartment->photos()->with('medium')->orderBy('order')->get();
        });
    }

    public function setMainPhoto(int $apartmentId, int $mediumId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        return DB::transaction(function () use ($apartment, $mediumId) {
            // إزالة main من جميع الصور
            $apartment->photos()->update(['is_main' => false]);

            // تعيين الصورة الجديدة كـ main
            $mainPhoto = $apartment->photos()
                ->where('medium_id', $mediumId)
                ->firstOrFail();

            $mainPhoto->update(['is_main' => true]);

            return $mainPhoto->load('medium');
        });
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

        DB::transaction(function () use ($apartment, $mediumId) {
            $photo = $apartment->photos()
                ->where('medium_id', $mediumId)
                ->firstOrFail();

            $deletedOrder = $photo->order;
            $photo->delete();

            $apartment->photos()
                ->where('order', '>', $deletedOrder)
                ->decrement('order');

            if (! $apartment->photos()->where('is_main', true)->exists()) {
                $firstPhoto = $apartment->photos()->orderBy('order')->first();
                if ($firstPhoto) {
                    $firstPhoto->update(['is_main' => true]);
                }
            }
        });
    }
}
