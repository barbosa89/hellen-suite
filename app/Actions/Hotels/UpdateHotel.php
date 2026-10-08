<?php

declare(strict_types=1);

namespace App\Actions\Hotels;

use App\Models\Hotel;
use Illuminate\Support\Arr;
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

        $this->hotel->update(Arr::except($this->data, ['establishment_code', 'credential', 'compliance_enabled']));

        new SyncHotelComplianceProfile()->execute(
            $this->hotel->refresh(),
            Arr::only($this->data, ['establishment_code', 'credential', 'compliance_enabled']),
        );

        return $this->hotel;
    }
}
