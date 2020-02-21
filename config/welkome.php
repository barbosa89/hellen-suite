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
        'vouchers' => [
            'id',
            'number',
            'origin',
            'destination',
            'discount',
            'subvalue',
            'taxes',
            'value',
            'open',
            'status',
            'payment_status',
            'losses',
            'reservation',
            'type',
            'made_by',
            'comments',
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
            'profession',
            'gender',
            'birthdate',
            'email',
            'address',
            'phone',
            'responsible_adult',
            'identification_type_id',
            'country_id',
            'user_id',
            'status',
            'banned',
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
            'user_id',
            'hotel_id'
        ],
        'services' => [
            'id',
            'description',
            'price',
            'status',
            'is_dining_service',
            'user_id',
            'hotel_id'
        ],
        'companies' => [
            'id',
            'tin',
            'business_name',
            'email',
            'phone',
            'address',
            'mobile',
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
        ],
        'assets' => [
            'id',
            'number',
            'description',
            'brand',
            'model',
            'serial_number',
            'location',
            'price',
            'user_id',
            'created_at',
            'hotel_id',
            'room_id'
        ],
        'props' => [
            'id',
            'description',
            'quantity',
            'price',
            'status',
            'hotel_id',
            'user_id'
        ],
        'maintenances' => [
            'id',
            'date',
            'commentary',
            'value',
            'invoice',
            'maintainable_id',
            'maintainable_type',
            'created_at',
            'user_id'
        ],
        'vehicles' => [
            'id',
            'registration',
            'brand',
            'color',
            'vehicle_type_id',
            'user_id',
            'created_at',
        ],
        'payments' => [
            'id',
            'date',
            'commentary',
            'payment_method',
            'value',
            'invoice',
            'voucher_id',
            'created_at',
        ],
        'shifts' => [
            'id',
            'cash',
            'open',
            'team_member',
            'hotel_id',
            'user_id',
            'created_at',
            'updated_at',
            'closed_at'
        ],
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
        'vouchers',
        'payments',
        'products',
        'rooms',
        'services',
        'shifts',
        'vehicles',
        'props',
        'dining',
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
            'vouchers'  => '*',
            'payments'  => '*',
            'products'  => '*',
            'rooms'     => '*',
            'services'  => '*',
            'shifts'    => '*',
            'vehicles'  => '*',
        ],
        'admin' => [
            'assets'    => '*',
            'vouchers'  => '*',
            'payments'  => '*',
            'products'  => '*',
            'services'  => '*',
        ],
        'receptionist'  => [
            'companies' => ['index', 'create', 'show', 'edit'],
            'guests'    => ['index', 'create', 'show', 'edit'],
            'vouchers'  => ['index', 'create', 'show'],
            'payments'  => ['index', 'create'],
            'products'  => ['index'],
            'rooms'     => ['index'],
            'shifts'    => ['index', 'create', 'show', 'edit'],
            'vehicles'  => ['index', 'create', 'show', 'edit'],
        ],
        // 'accountant'
    ]
];
