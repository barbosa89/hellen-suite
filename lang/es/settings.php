<?php

declare(strict_types=1);

return [
    'title' => 'Configuración',
    'actions' => ['configure_currency' => 'Configurar moneda'],
    'pages' => [
        'edit' => [
            'heading' => 'Configuración de la aplicación',
            'description' => 'Define las preferencias globales que utiliza Hellen Suite.',
        ],
    ],
    'currency' => [
        'title' => 'Moneda de referencia',
        'description' => 'Selecciona la moneda que identifica las tarifas de referencia de todas las habitaciones.',
        'label' => 'Moneda',
        'placeholder' => 'Selecciona una moneda',
        'hint' => 'La selección admite todos los códigos ISO 4217 y las tarifas se guardan con dos decimales.',
        'reference_title' => 'Uso referencial',
        'reference_description' => 'Cambiar la moneda no convierte ni modifica las tarifas existentes. El código seleccionado solo indica cómo deben interpretarse y mostrarse.',
    ],
    'messages' => [
        'updated' => 'La configuración de moneda fue actualizada.',
        'currency_required' => 'Configura una moneda antes de crear o editar habitaciones.',
    ],
];
