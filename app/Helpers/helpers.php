<?php

use App\Helpers\Fields;
use App\Helpers\Id;
use App\Helpers\Notary;
use App\Helpers\Parameter;
use App\Welkome\Hotel;

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