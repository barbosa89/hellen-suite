<?php

/**
 * Welkome app configuration
 */

return [

    /*
    |--------------------------------------------------------------------------
    | General parameters
    |--------------------------------------------------------------------------
    |
    | The application can be executed in two environments, in the web
    | environment it will execute some additional functionalities.
    |
    */

    'env' => env('ENVIRONMENT', 'web'),

    'paginate' => 20,

    'fields' => [
        'hotels' => [
            'id',
            'business_name',
            'tin',
            'address',
            'phone',
            'mobile',
            'email',
            'user_id',
            'status',
            'image',
            'created_at',
            'main_hotel'
        ],
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
            'company_id',
            'user_id',
            'hotel_id',
            'created_at'
        ],
        'rooms' => [
            'id',
            'number',
            'description',
            'price',
            'status',
            'user_id',
            'tax_status',
            'tax',
            'is_suite',
            'capacity',
            'floor',
            'min_price',
            'hotel_id'
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
            'status',
            'created_at'
        ],
        'products' => [
            'id',
            'description',
            'brand',
            'reference',
            'price',
            'quantity',
            'status',
            'user_id'
        ],
        'services' => [
            'id',
            'description',
            'price',
            'status',
            'user_id'
        ],
        'companies' => [
            'id',
            'tin',
            'business_name',
            'user_id',
            'created_at'
        ],
        'users' => [
            'id',
            'name',
            'email',
            'password',
            'token',
            'parent',
            'created_at',
            'updated_at',
            'email_verified_at'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | System modules
    |--------------------------------------------------------------------------
    |
    | All modules to create automatically all restful permissions
    |
    */
    'modules' => [
        'team',
        'assets',
        'companies',
        'guests',
        'hotels',
        'invoices',
        'payments',
        'products',
        'rooms',
        'services',
        'shifts',
        'vehicles',
    ],

    /*
    |--------------------------------------------------------------------------
    | System roles
    |--------------------------------------------------------------------------
    |
    | Basic system roles
    |
    */
    'roles' => [
        'root',
        'manager',
        'admin',
        'receptionist',
        'accountant'
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions assignment to roles
    |--------------------------------------------------------------------------
    |
    | ONLY FOR GUIDE
    | Basic system roles
    |
    */
    'permissions' => [
        // Root permissions do not apply, only with role
        'root' => [
            'users'                 => '*',
            'subscriptions'         => '*',
            'identification_types'  => '*'
        ],
        'manager' => [
            'members'   => '*',
            'assets'    => '*',
            'companies' => '*',
            'guests'    => '*',
            'hotels'    => '*',
            'invoices'  => '*',
            'payments'  => '*',
            'products'  => '*',
            'rooms'     => '*',
            'services'  => '*',
            'shifts'    => '*',
            'vehicles'  => '*',
        ],
        'admin' => [
            'assets'    => '*',
            'invoices'  => '*',
            'payments'  => '*',
            'products'  => '*',
            'services'  => '*',
        ],
        'receptionist'  => [
            'companies' => ['index', 'create', 'show', 'edit'],
            'guests'    => ['index', 'create', 'show', 'edit'],
            'invoices'  => ['index', 'create', 'show'],
            'payments'  => ['index', 'create'],
            'products'  => ['index'],
            'rooms'     => ['index'],
            'shifts'    => ['index', 'create', 'show', 'edit'],
            'vehicles'  => ['index', 'create', 'show', 'edit'],
        ],
        // 'accountant'
    ]
];
