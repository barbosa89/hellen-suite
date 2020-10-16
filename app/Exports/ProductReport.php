<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductReport implements FromView
{
    /**
     * The Product instance.
     *
     * @var \App\Models\Product
     */
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function view(): View
    {
        return view('app.products.exports.product', [
            'product' => $this->product
        ]);
    }
}
