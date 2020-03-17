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
            'is_supplier',
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
        'accountant',
        'cashier'
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
            'team'      => '*',
            'assets'    => '*',
            'companies' => '*',
            'guests'    => '*',
            'hotels'    => '*',
            'vouchers'  => ['*', 'open', 'close'],
            'products'  => ['*', 'vouchers'],
            'rooms'     => '*',
            'services'  => '*',
            'shifts'    => ['index', 'create', 'show'],
            'vehicles'  => '*',
            'props'     => ['*', 'vouchers'],
            'dining'    => ['*', 'sale'],
            'payments'  => ['*', 'close'],
            'transactions'  => ['sale', 'entry', 'loss', 'discard']
        ],
        'admin' => [
            'assets'    => '*',
            'companies' => '*',
            'guests'    => '*',
            'hotels'    => ['index'],
            'vouchers'  => ['*', 'open', 'close'],
            'products'  => ['*', 'vouchers'],
            'rooms'     => '*',
            'services'  => '*',
            'shifts'    => ['index', 'create', 'show'],
            'vehicles'  => '*',
            'props'     => ['*', 'vouchers'],
            'dining'    => ['*', 'sale'],
            'payments'  => ['*', 'close'],
            'transactions'  => ['sale', 'entry', 'loss', 'discard']
        ],
        'receptionist'  => [
            'companies'     => ['index', 'create', 'show', 'edit'],
            'guests'        => ['index', 'create', 'show', 'edit'],
            'hotels'        => ['index'],
            'vouchers'      => ['index', 'create', 'show', 'edit', 'destroy', 'close'],
            'payments'      => ['index', 'create', 'edit', 'destroy', 'close'],
            'products'      => ['index'],
            'rooms'         => ['index'],
            'services'      => ['index'],
            'shifts'        => ['index', 'create', 'show'],
            'vehicles'      => ['index', 'create', 'show', 'edit'],
            'transactions'  => ['sale', 'loss']
        ],
        'accountant' => [
            'assets'        => ['index'],
            'companies'     => ['index'],
            'guests'        => ['index'],
            'hotels'        => ['index'],
            'vouchers'      => ['index'],
            'payments'      => ['index'],
            'products'      => ['index'],
            'services'      => ['index'],
            'props'         => ['index'],
            'dining'        => ['index']
        ],
        'cashier' => [
            'companies'     => ['index'],
            'guests'        => ['index'],
            'hotels'        => ['index'],
            'vouchers'      => ['index', 'show'],
            'payments'      => ['index', 'show'],
            'products'      => ['index'],
            'dining'        => ['index', 'sale']
        ],
    ],

    'colors' => [
        'loss' => [
            'bar' => 'rgba(255, 102, 102, 0.2)',
            'border' => 'rgba(255, 102, 102, 1)'
        ],
        'entry' => [
            'bar' => 'rgba(5, 32, 74, 0.2)',
            'border' => 'rgba(5, 32, 74, 1)'
        ],
        'lodging' => [
            'bar' => 'rgba(117, 185, 190, 0.2)',
            'border' => 'rgba(117, 185, 190, 1)'
        ],
        'discard' => [
            'bar' => 'rgba(245, 158, 62, 0.2)',
            'border' => 'rgba(245, 158, 62, 1)'
        ],
        'dining' => [
            'bar' => 'rgba(255, 211, 218, 0.2)',
            'border' => 'rgba(255, 211, 218, 1)'
        ],
        'sale' => [
            'bar' => 'rgba(35, 206, 107, 0.2)',
            'border' => 'rgba(35, 206, 107, 1)'
        ],
    ]
];