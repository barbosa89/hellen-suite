<?php

declare(strict_types=1);

namespace App\Actions\Hotels;

use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;

class UpdateHotel extends CreateHotel
{
    public function __construct(
        protected Hotel $hotel,
        protected array $data
    ) {}

    public function execute(): Hotel
    {
        if (isset($this->data['image'])) {
            $this->data['image'] = $this->storeImage($this->data['image']);

            if ($image = $this->hotel->getRawOriginal('image')) {
                Storage::disk('public')->delete($image);
            }
        }

        $this->hotel->update($this->data);

        return $this->hotel;
    }
}
