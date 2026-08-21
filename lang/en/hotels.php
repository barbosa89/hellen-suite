<?php

declare(strict_types=1);

return [
    'title' => 'Hotels',
    'directory' => 'Hotel directory',
    'selected_hotel' => 'Selected hotel',

    'actions' => [
        'view' => 'View hotel',
        'edit' => 'Edit hotel',
        'delete' => 'Delete hotel',
        'create' => 'Create hotel',
        'manage' => 'Manage',
        'open' => 'Open hotel',
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
            'description' => 'Manage your properties and enter each hotel workspace.',
            'directory_label' => 'Registered hotel directory',
            'registered' => 'registered hotel|registered hotels',
            'contact' => 'Contact',
            'hotel_identifier' => 'Hotel #:id',
            'open_actions' => 'Open actions for :name',
            'no_hotels' => 'No hotels registered yet',
            'no_hotels_hint' => 'Add your first hotel to keep the registry up to date.',
            'delete_title' => 'Delete hotel',
            'delete_confirm' => 'Are you sure you want to delete ":name"?',
        ],
        'create' => [
            'heading' => 'Create hotel',
            'description' => 'Register the identity, location, and contact details of the new property.',
        ],
        'edit' => [
            'heading' => 'Edit hotel',
            'description' => 'Update the information registered for :name.',
        ],
        'show' => [
            'description' => 'Review the legal and contact information registered for this property.',
            'no_image' => 'This hotel does not have an image yet.',
        ],
    ],

    'form' => [
        'identity' => [
            'title' => 'Hotel identity',
            'description' => 'These details identify the property legally and commercially.',
        ],
        'contact' => [
            'title' => 'Location and contact',
            'description' => 'Keep the channels used by the team for daily operations available.',
        ],
        'image' => [
            'title' => 'Property image',
            'description' => 'Use a recognizable photo to find the hotel quickly in the directory.',
        ],
    ],

    'management' => [
        'heading' => 'Hotel overview',
        'description' => 'Information and available modules for :name.',
        'contact_phone' => 'Contact phone',
        'profile_status' => 'Profile status',
        'profile_progress' => ':completed of :total main details registered.',
        'complete_profile' => 'Complete information',
        'modules' => 'Hotel modules',
        'modules_description' => 'Open the tools that work exclusively with this property.',
        'open_module' => 'Open module',
    ],

    'messages' => [
        'created' => 'Hotel created successfully.',
        'updated' => 'Hotel updated successfully.',
        'deleted' => 'Hotel deleted successfully.',
    ],

];
