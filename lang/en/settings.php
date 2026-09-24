<?php

declare(strict_types=1);

return [
    'title' => 'Settings',
    'actions' => ['configure_currency' => 'Configure currency'],
    'pages' => [
        'edit' => [
            'heading' => 'Application settings',
            'description' => 'Define the global preferences used by Hellen Suite.',
        ],
    ],
    'currency' => [
        'title' => 'Reference currency',
        'description' => 'Select the currency that identifies reference rates for every room.',
        'label' => 'Currency',
        'placeholder' => 'Select a currency',
        'no_results' => 'No currencies match your search.',
        'hint' => 'All ISO 4217 codes are available and rates are stored with two decimals.',
        'reference_title' => 'Reference use',
        'reference_description' => 'Changing currency does not convert or alter existing rates. The selected code only indicates how they are interpreted and displayed.',
    ],
    'messages' => [
        'updated' => 'Currency settings were updated.',
        'configuration_required' => 'Complete the required settings to continue. Pending fields are marked below.',
    ],
];
