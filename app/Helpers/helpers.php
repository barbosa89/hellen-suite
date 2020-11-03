<?php

use App\Helpers\Id;
use App\Models\Hotel;
use App\Helpers\Fields;
use App\Helpers\Notary;
use App\Helpers\Parameter;
use Illuminate\Support\Str;
use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

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

if (!function_exists('param_clean')) {
    function param_clean($value = null)
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

if (!function_exists('get_pdf_printer')) {
    /**
     * Build Snappy PDF Printer
     *
     * @param array $margins
     * @return Barryvdh\Snappy\PdfWrapper
     */
    function get_pdf_printer(array $margins = [5, 5, 5, 5]): PdfWrapper
    {
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('images', true);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('margin-top', $margins[0] ??= 5);
        $pdf->setOption('margin-bottom', $margins[1] ??= 5);
        $pdf->setOption('margin-left', $margins[2] ??= 5);
        $pdf->setOption('margin-right', $margins[3] ??= 5);

        return $pdf;
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
