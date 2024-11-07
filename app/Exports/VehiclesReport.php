<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class VehiclesReport implements FromView
{
    /**
     * The Company collection.
     *
     * @var \App\Models\Company
     */
    protected $vehicles;

    public function __construct(Collection $vehicles)
    {
        $this->vehicles = $vehicles;
    }

    public function view(): View
    {
        return view('app.vehicles.exports.vehicles', [
            'vehicles' => $this->vehicles,
        ]);
    }
}
