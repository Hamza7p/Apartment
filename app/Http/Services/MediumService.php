<?php

namespace App\Http\Services;

use App\Models\Medium;
use App\Http\Services\Base\CrudService;
use App\Models\Base\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediumService extends CrudService
{
    protected function getModelClass(): string
    {
        return Medium::class;
    }

    public function create(array $data): BaseModel
    {
        /** @var \Illuminate\Http\UploadedFile $medium */
        $medium = $data['medium'];

        $path = 'media/' . str_replace('-', '/', $data['for']);
        $mediumName = $medium->hashName();
        $extension = $medium->getClientOriginalExtension();
        $originName = $medium->getClientOriginalName();

        $medium->storeAs(
            $path,
            $mediumName,
            ['disk' => 'public']
        );

        return parent::create([
            'path' => $path . '/' . $mediumName,
            'extension' => $extension,
            'for' => $data['for'],
            'type' => $data['type'],
            'name' => $originName,
        ])->refresh();
    }

    public function createMultiple($data): array
    {
        return DB::transaction(function () use ($data) {
            $media = [];

            foreach ($data['media'] as $medium) {

                $media[] = $this->create($medium);
            }

            return $media;
        });
    }


    public function destroy(mixed $medium): void
    {
        if (!$medium instanceof Medium) {
            $medium = Medium::findorfail($medium);
        }

        if (Storage::exists($medium->disk->value . '/' . $medium->path)) {
            Storage::delete($medium->disk->value . '/' . $medium->path);
            $medium->delete();
        }
    }
}
