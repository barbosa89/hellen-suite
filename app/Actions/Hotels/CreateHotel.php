<?php

declare(strict_types=1);

namespace App\Actions\Hotels;

use App\Models\Hotel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CreateHotel
{
    public function __construct(
        protected array $data
    ) {}

    public function execute(): Hotel
    {
        if (isset($this->data['image'])) {
            $this->data['image'] = $this->storeImage($this->data['image']);
        }

        return Hotel::create($this->data);
    }

    protected function storeImage(UploadedFile $upload): string
    {
        $image = Image::decode($upload);
        $path = Arr::join(['hotels', Str::random(40).'.'.$upload->getClientOriginalExtension()], DIRECTORY_SEPARATOR);

        Storage::put($path, $image->encodeUsingFileExtension($upload->getClientOriginalExtension()));

        return $path;
    }
}
