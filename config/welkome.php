<?php

/**
 * Welkome app configuration
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Execution environment
    |--------------------------------------------------------------------------
    |
    | The application can be executed in two environments, in the web 
    | environment it will execute some additional functionalities.
    |
    */

    'env' => env('ENVIRONMENT', 'web'),

    'paginate' => 20, 

    'fields' => [
        'invoices' => [
            'id', 
            'number', 
            'discount', 
            'subvalue', 
            'taxes', 
            'value', 
            'open', 
            'status',
            'reservation',
            'for_company',
            'are_tourists',
            'company_id',
            'guest_id',
            'user_id',
        ],
        'rooms' => [
            'id', 
            'number', 
            'description', 
            'value', 
            'status', 
            'user_id'
        ],
        'guests' => [
            'id',
            'dni',
            'name',
            'last_name',
            'gender',
            'birthdate',
            'responsible_adult',
            'identification_type_id',
            'user_id',
        ]
    ],
];
