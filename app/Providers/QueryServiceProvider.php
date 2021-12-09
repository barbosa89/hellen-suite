<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class QueryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('findHash', function (string $hash, array $columns = []) {
            return $this->where('id', id_decode($hash))
                ->where('user_id', id_parent())
                ->firstOrFail($columns);
        });
    }
}
