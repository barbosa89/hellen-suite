<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Shift;
use App\Models\Voucher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Observers\VoucherObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
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
        Paginator::useBootstrap();

        // Custom validation
        Validator::extend('stock', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $product = id_decode($data['product']);
            $product = DB::table('products')->where('id', $product)
                ->select('id', 'quantity')->first();

            return (int) $value <= $product->quantity;
        });

        // Check if encoded ID exists
        Validator::extend('hashed_exists', function ($attribute, $value, $parameters, $validator) {
            $value = id_decode($value);
            $table = $parameters[0];
            $field = $parameters[1];
            $result = DB::table($table)->where($field, $value)
                ->select('id')->get();

            return $result->count() === 1;
        });

        // Check if the hotel to be stored is an independent headquarters or hotel
        Validator::extend('headquarters', function ($attribute, $value, $parameters, $validator) {
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

                // Hotel is a headquarters
                if ($data['type'] == 'headquarters') {
                    return $hotel->count() === 1;
                }
            }

            return false;
        });

        // Check if the receptionist has an assigned headquarters
        Validator::extend('has_headquarters', function ($attribute, $value, $parameters, $validator) {
            $user = User::where('email', $value)
                ->whereHas('roles', function ($query)
                {
                    $query->where('name', 'receptionist');
                })->with([
                    'headquarters' => function($query) {
                        $query->select(['id', 'business_name']);
                    }
                ])->first(['id', 'email']);

            if (!empty($user)) {
                return $user->headquarters->isNotEmpty();
            }

            return true;
        });

        // Check if exists an open shift
        Validator::extend('open_shift', function ($attribute, $value, $parameters, $validator) {
            // Query all open shifts for authenticated user
            $shifts = Shift::open()->get(['id', 'open', 'hotel_id']);

            // True if there are no open shifts
            // Shift will be created
            if ($shifts->isEmpty()) {
                return true;
            }

            if (is_string($value)) {
                $value = id_decode($value);
            }

            // Check if the user owns the shiftin the hotel
            if ($shifts->where('hotel_id', $value)->count() === 1) {
                return true;
            }

            // User is not the shift owner
            return false;
        });

        /**
         * $parameters[0]   Table name
         * $parameters[1]   The parent field
         * $parameters[2]   Except
         */
        Validator::extend('unique_with', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();

            // Parent field
            // $alias[0]: Parent field name in the form
            // $alias[1]: Parent field name in table table
            $alias = explode('#', $parameters[1]);
            $parentField = isset($alias[1]) ? $alias[1] : $alias[0];
            $parentId = is_string($data[$alias[0]]) ? id_decode($data[$alias[0]]) : $data[$alias[0]];
            $exception = isset($parameters[2]) ? (int) trim($parameters[2]) : null;

            $results = DB::table($parameters[0])
                ->where($attribute, $value)
                ->where($parentField, $parentId)
                ->get(['id']);

            // Update method: Only must be exists one record in the table
            if (!empty($exception)) {
                if ($results->count() === 1 and $results->first()->id === $exception) {
                    return true;
                }

                return false;
            }

            return $results->count() === 0;
        });

        /**
         * $parameters[0]   Table name
         * $parameters[1]   The unique per user field
         * $parameters[2]   Except
         */
        Validator::extend('unique_per_user', function ($attribute, $value, $parameters, $validator) {
            $exception = isset($parameters[2]) ? (int) trim($parameters[2]) : null;

            $results = DB::table($parameters[0])
                ->where($parameters[1], $value)
                ->where('user_id', id_parent())
                ->get(['id']);

            // Update method: Only must be exists one record in the table
            if (!empty($exception)) {
                if ($results->isEmpty()) {
                    return true;
                }

                if ($results->count() === 1 and $results->first()->id === $exception) {
                    return true;
                }

                return false;
            }

            return $results->count() === 0;
        });

        Validator::extend('verified', function($attribute, $value, $parameters, $validator)
        {
            $users = DB::table('users')
                ->where($attribute, $value)
                ->where('email_verified_at', '!=', null)
                ->select('id', 'email', 'email_verified_at')
                ->get();

            return $users->count() === 1;
        });

        Validator::extend('price', function($attribute, $value, $parameters, $validator)
        {
            $data = $validator->getData();

            if (isset($data[$parameters[1]])) {
                $id = $data[$parameters[1]];
            } else {
                $keys = explode(".", $attribute);
                $id = $data[$keys[0]][$keys[1]][$parameters[1]];
            }

            $room = DB::table($parameters[0])
                ->where($parameters[1], $id)
                ->where('hotel_id', id_decode($data['hotel']))
                ->first(['id', 'price', 'min_price']);

            if ($value >= $room->min_price && $value <= $room->price) {
                return true;
            }

            return false;
        });

        // Observers

        Voucher::observe(VoucherObserver::class);

        // Macros

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
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
        $this->app->bind(\App\Contracts\VoucherPrinter::class, \App\Services\VoucherPrinter::class);
        $this->app->bind(\App\Contracts\RoomRepository::class, \App\Repositories\RoomRepository::class);
        $this->app->bind(\App\Contracts\GuestRepository::class, \App\Repositories\GuestRepository::class);
        $this->app->bind(\App\Contracts\CompanyRepository::class, \App\Repositories\CompanyRepository::class);
        $this->app->bind(\App\Contracts\VoucherRepository::class, \App\Repositories\VoucherRepository::class);
    }
}
