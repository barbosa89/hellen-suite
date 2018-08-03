<?php

namespace App\Providers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('stock', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $product = Hashids::decode($data['product']);
            $product = \DB::table('products')->where('id', $product)
                ->select('id', 'quantity')->first();
            // dd($value <= $product->quantity);
            return (int) $value <= $product->quantity;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
