<?php

use App\Helpers\Id;
use App\Helpers\Parameter;

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
    function param_clean($value)
    {
        return Parameter::clean($value);
    }
}