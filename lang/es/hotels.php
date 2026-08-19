<?php

return [
    'title' => 'Hoteles',

    'actions' => [
        'view' => 'Ver hotel',
        'edit' => 'Editar hotel',
        'delete' => 'Eliminar hotel',
        'create' => 'Crear hotel',
    ],

    'fields' => [
        'id' => [
            'label' => 'ID',
        ],
        'business_name' => [
            'label' => 'Nombre comercial',
        ],
        'tin' => [
            'label' => 'NIT',
        ],
        'address' => [
            'label' => 'Dirección',
        ],
        'phone' => [
            'label' => 'Teléfono',
        ],
        'mobile' => [
            'label' => 'Móvil',
        ],
        'email' => [
            'label' => 'Correo electrónico',
        ],
        'image' => [
            'label' => 'Imagen',
        ],
    ],

    'pages' => [
        'index' => [
            'registered' => 'hotel registrado|hoteles registrados',
            'no_hotels' => 'No hay hoteles registrados aún',
            'no_hotels_hint' => 'Agrega tu primer hotel para mantener el registro actualizado.',
            'delete_confirm' => '¿Estás seguro de que deseas eliminar ":name"?',
        ],
        'create' => [
            'heading' => 'Crear hotel',
        ],
        'edit' => [
            'heading' => 'Editar hotel',
        ],
    ],

    'messages' => [
        'created' => 'Hotel creado con éxito.',
        'updated' => 'Hotel actualizado con éxito.',
        'deleted' => 'Hotel eliminado con éxito.',
    ],

];
