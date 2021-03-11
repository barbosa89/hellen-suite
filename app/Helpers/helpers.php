<?php

use App\Helpers\Id;
use App\Models\Hotel;
use App\Helpers\Fields;
use App\Helpers\Notary;
use App\Helpers\Parameter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

if (!function_exists('id_encode')) {
    function id_encode(string $id)
    {
        return Id::encode($id);
    }
}

if (!function_exists('id_decode')) {
    function id_decode(string $id)
    {
        return Id::decode($id);
    }
}

if (!function_exists('id_decode_recursive')) {
    function id_decode_recursive(array $ids)
    {
        return Id::pool($ids);
    }
}

if (!function_exists('id_parent')) {
    function id_parent()
    {
        return Id::parent();
    }
}

if (!function_exists('clean_param')) {
    function clean_param($value = null)
    {
        return Parameter::clean($value);
    }
}

if (!function_exists('notary')) {
    function notary(Hotel $hotel)
    {
        return Notary::create($hotel);
    }
}

if (!function_exists('fields_get')) {
    function fields_get(string $model)
    {
        return Fields::get($model);
    }
}

if (!function_exists('fields_dotted')) {
    function fields_dotted(string $model)
    {
        return Fields::parsed($model);
    }
}

if (!function_exists('argument_array')) {
    function argument_array($args)
    {
        if (is_array($args[0])) {
            return $args[0];
        }

        return $args;
    }
}

if (!function_exists('get_columns')) {
    /**
     * Get all table column names from schema
     *
     * @param string $table
     * @param boolean $dotted
     * @return array
     */
    function get_columns(string $table, bool $dotted = false): array
    {
        $columns = Schema::getColumnListing($table);

        if ($dotted) {
            array_walk($columns, function (&$column) use ($table) {
                $column = $table . '.' . $column;
            });
        }

        return $columns;
    }
}

if (!function_exists('external_url')) {
    /**
     * Generate external URL with query params
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    function external_url(string $url, array $params = []): string
    {
        $url = Str::of($url)->finish('/');

        $params = collect($params)->transform(function ($value, $key) {
            return urlencode($key) . '=' . urlencode($value);
        })->join('&');

        return $url . '?' . $params;
    }
}

if (!function_exists('cents_to_float')) {
    /**
     * Convert amount in cents to float number
     *
     * @param string $value
     * @return float
     */
    function cents_to_float(string $value): float
    {
        $value = (float) $value;

        if ($value > 0) {
            return ($value / 100);
        }

        throw new InvalidArgumentException("The value must be greater than zero", 1);
    }
}

if (!function_exists('get_user_permissions')) {
    /**
     * Get all asigned user permissions
     *
     * @return array
     */
    function get_user_permissions(): array
    {
        if (Auth::check()) {
            $permissions = Permission::whereHas('users', function ($query) {
                $query->where('id', Auth::id());
            })->get(['id', 'name']);

            return $permissions->pluck('name')->toArray();
        }

        return [];
    }
}

if (!function_exists('get_colors')) {
    /**
     * @return array
     */
    function get_colors(): array
    {
        $colors = [];

        foreach (config('settings.colors') as $item) {
            $colors[] = $item;
        }

        return $colors;
    }
}
