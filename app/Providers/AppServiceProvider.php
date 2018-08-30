<?php

namespace App\Providers;

use App\Welkome\Invoice;
use App\Observers\InvoiceObserver;
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
        // Custom validation

        Validator::extend('stock', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $product = Hashids::decode($data['product']);
            $product = \DB::table('products')->where('id', $product)
                ->select('id', 'quantity')->first();

            return (int) $value <= $product->quantity;
        });

        // Observers

        Invoice::observe(InvoiceObserver::class);
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
