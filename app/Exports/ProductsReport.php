<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsReport implements FromView
{
    /**
     * The products collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $hotels;

    public function __construct(Collection $hotels)
    {
        $this->hotels = $hotels;
    }

    public function view(): View
    {
        return view('app.products.exports.products', [
            'hotels' => $this->hotels
        ]);
    }
}
