<?php

namespace App\Providers;

use App\Welkome\Invoice;
use App\Observers\InvoiceObserver;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
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

        // Macros

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (array_wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
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
