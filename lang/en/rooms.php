<?php

declare(strict_types=1);

return [
    'title' => 'Rooms',
    'module_navigation' => 'Inventory navigation',
    'actions' => [
        'create' => 'Create room',
        'edit' => 'Edit room',
        'delete' => 'Delete room',
        'activate' => 'Activate room',
        'deactivate' => 'Deactivate room',
    ],
    'fields' => [
        'number' => ['label' => 'Number'],
        'room_type' => ['label' => 'Type'],
        'floor' => ['label' => 'Floor'],
        'reference_price' => ['label' => 'Reference rate'],
        'housekeeping_status' => ['label' => 'Housekeeping status'],
        'is_active' => ['label' => 'Active room'],
        'operation' => ['label' => 'Operation'],
    ],
    'housekeeping' => ['clean' => 'Clean', 'dirty' => 'Needs cleaning'],
    'activity' => ['active' => 'Active', 'inactive' => 'Inactive'],
    'pages' => [
        'index' => [
            'heading' => 'Rooms for :hotel',
            'description' => 'Review and update the operational inventory for :hotel.',
            'inventory_label' => 'Room inventory',
            'empty_title' => 'No rooms have been registered yet',
            'empty' => 'Create the room types used by this property, then register each room.',
            'currency_required_title' => 'Set the currency before registering rates',
            'currency_required' => 'The reference rate needs a global currency. Set it now and return to the inventory.',
            'delete_title' => 'Delete room',
            'delete_confirm' => 'Are you sure you want to delete room :number?',
            'open_actions' => 'Open actions for room :number',
        ],
        'create' => ['heading' => 'Create room', 'description' => 'Register its type, location, rate, and initial operating status.'],
        'edit' => ['heading' => 'Edit room :number', 'description' => 'Update this room’s administrative and operational details.'],
    ],
    'form' => [
        'assignment' => ['title' => 'Identification and location', 'description' => 'Associate the room with the type that matches its physical configuration.'],
        'operation' => ['title' => 'Rate and operation', 'description' => 'The rate is for reference, and housekeeping can be updated directly from inventory.'],
        'select_type' => 'Select a room type',
        'reference_price_hint' => 'Stores up to two decimals in the configured currency.',
        'activity_hint' => 'Inactive rooms remain in the inventory.',
    ],
    'messages' => [
        'created' => 'Room created successfully.',
        'updated' => 'Room updated successfully.',
        'deleted' => 'Room deleted successfully.',
        'activity_updated' => 'Room activity was updated.',
        'housekeeping_updated' => 'Housekeeping status was updated.',
    ],
];
