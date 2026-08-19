<?php

return [
    'title' => 'Hotels',

    'actions' => [
        'view' => 'View hotel',
        'edit' => 'Edit hotel',
        'delete' => 'Delete hotel',
        'create' => 'Create hotel',
    ],

    'fields' => [
        'id' => [
            'label' => 'ID',
        ],
        'business_name' => [
            'label' => 'Business name',
        ],
        'tin' => [
            'label' => 'TIN',
        ],
        'address' => [
            'label' => 'Address',
        ],
        'phone' => [
            'label' => 'Phone',
        ],
        'mobile' => [
            'label' => 'Mobile',
        ],
        'email' => [
            'label' => 'Email',
        ],
        'image' => [
            'label' => 'Image',
        ],
    ],

    'pages' => [
        'index' => [
            'registered' => 'registered hotel|registered hotels',
            'no_hotels' => 'No hotels registered yet',
            'no_hotels_hint' => 'Add your first hotel to keep the registry up to date.',
            'delete_confirm' => 'Are you sure you want to delete ":name"?',
        ],
        'create' => [
            'heading' => 'Create hotel',
        ],
        'edit' => [
            'heading' => 'Edit hotel',
        ],
    ],

    'messages' => [
        'created' => 'Hotel created successfully.',
        'updated' => 'Hotel updated successfully.',
        'deleted' => 'Hotel deleted successfully.',
    ],

];
