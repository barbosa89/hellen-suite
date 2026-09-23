<?php

declare(strict_types=1);

return [
    'title' => 'Room types',
    'capacity' => '{count} guest|{count} guests',
    'rooms_count' => '{count} room|{count} rooms',
    'actions' => [
        'create' => 'Create type',
        'edit' => 'Edit type',
        'delete' => 'Delete type',
    ],
    'fields' => [
        'name' => [
            'label' => 'Name',
        ],
        'capacity' => [
            'label' => 'Capacity',
        ],
        'rooms_count' => [
            'label' => 'Linked rooms',
        ],
    ],
    'pages' => [
        'index' => [
            'heading' => 'Room types for :hotel',
            'description' => 'Define the configurations that can be assigned to the inventory for :hotel.',
            'empty_title' => 'There are no room types yet',
            'empty' => 'Create the first type to start registering physical rooms.',
            'delete_title' => 'Delete room type',
            'delete_confirm' => 'Are you sure you want to delete “:name”?',
            'delete_disabled' => 'This type cannot be deleted while it has linked rooms.',
        ],
        'create' => [
            'heading' => 'Create room type',
            'description' => 'Define the name and capacity that distinguish this configuration.',
        ],
        'edit' => ['heading' => 'Edit :name', 'description' => 'Update this configuration’s name or capacity.'],
    ],
    'form' => [
        'title' => 'Type configuration',
        'description' => 'These details describe an inventory category, not a physical room.',
    ],
    'messages' => [
        'capacity_blocked' => 'Capacity cannot be lower than an active or confirmed guest assignment.',
        'created' => 'Room type created successfully.',
        'updated' => 'Room type updated successfully.',
        'deleted' => 'Room type deleted successfully.',
        'delete_blocked' => 'A type with linked rooms cannot be deleted.',
    ],
];
