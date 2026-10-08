<?php

declare(strict_types=1);

return [
    'title' => 'Guests',
    'actions' => ['create' => 'Register guest', 'edit' => 'Edit guest', 'view' => 'View profile'],
    'fields' => [
        'first_name' => ['label' => 'First names'],
        'second_first_name' => ['label' => 'Second first name'],
        'last_name' => ['label' => 'Last names'],
        'second_last_name' => ['label' => 'Second last name'],
        'identification_type' => ['label' => 'Document type'],
        'identification_number' => ['label' => 'Document number'],
        'birth_date' => ['label' => 'Birth date'],
        'gender' => ['label' => 'Gender', 'feminine' => 'Female', 'masculine' => 'Male'],
        'nationality' => ['label' => 'Nationality'],
        'residence_country' => ['label' => 'Residence country'],
        'mobile' => ['label' => 'Mobile'],
        'email' => ['label' => 'Email'],
    ],
    'pages' => [
        'index' => ['heading' => 'Guests', 'description' => 'Review and update the administrative profiles for :hotel.', 'search' => 'Search by name, document, or mobile', 'empty_title' => 'No guests have been registered yet', 'empty' => 'Guests created during check-in will appear here for administration.'],
        'create' => ['heading' => 'Register guest', 'description' => 'Create an administrative profile for a future stay.'],
        'edit' => ['heading' => 'Edit guest', 'description' => 'Correct this profile’s administrative details.'],
        'show' => ['heading' => 'Guest profile', 'stays' => 'Stays', 'empty_stays' => 'This guest has no registered stays yet.'],
    ],
    'form' => ['identity' => ['title' => 'Identification', 'description' => 'Identification prevents duplicate guests within this hotel.'], 'contact' => ['title' => 'Contact', 'description' => 'These optional details support guest service.'], 'select_identification_type' => 'Select a document type'],
    'messages' => ['created' => 'Guest registered successfully.', 'updated' => 'Guest profile updated successfully.'],
];
