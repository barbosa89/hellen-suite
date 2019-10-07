<?php

namespace App\Providers;

use App\User;
use App\Helpers\Id;
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
            $product = Id::get($data['product']);
            $product = \DB::table('products')->where('id', $product)
                ->select('id', 'quantity')->first();

            return (int) $value <= $product->quantity;
        });

        Validator::extend('hashed_exists', function ($attribute, $value, $parameters, $validator) {
            $value = Id::get($value);
            $table = $parameters[0];
            $field = $parameters[1];
            $result = \DB::table($table)->where($field, $value)
                ->select('id')->get();

            return $result->count() === 1;
        });

        Validator::extend('headquarter', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();

            if (isset($data['type'])) {
                $hotel = User::find(auth()->user()->id)->hotels()
                    ->where('tin', $value)
                    ->where('main_hotel', null)
                    ->get(['id', 'business_name', 'main_hotel']);

                // Is a standalone hotel
                if ($data['type'] == 'main') {
                    return $hotel->count() === 0;
                }

                // Hotel is a headquarter
                if ($data['type'] == 'headquarter') {
                    return $hotel->count() === 1;
                }
            }

            // Only must be exist the current object
            // $id = $parameters[0];
            // $hotel = User::find(auth()->user()->id)->hotels()
            //     ->where('id', $id)
            //     ->where('business_name', $data['business_name'])
            //     ->where('tin', $value)
            //     ->firstP(['id', 'business_name', 'main_hotel']);

            // return $hotel->count() === 1;
            return false;

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
