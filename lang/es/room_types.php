<?php

declare(strict_types=1);

return [
    'title' => 'Tipos de habitación',
    'capacity' => '{count} huésped|{count} huéspedes',
    'rooms_count' => '{count} habitación|{count} habitaciones',
    'actions' => ['create' => 'Crear tipo', 'edit' => 'Editar tipo', 'delete' => 'Eliminar tipo'],
    'fields' => ['name' => ['label' => 'Nombre'], 'capacity' => ['label' => 'Capacidad'], 'rooms_count' => ['label' => 'Habitaciones asociadas']],
    'pages' => [
        'index' => [
            'heading' => 'Tipos de habitación de :hotel',
            'description' => 'Define las configuraciones que se podrán asignar al inventario de :hotel.',
            'empty_title' => 'Aún no hay tipos de habitación',
            'empty' => 'Crea el primer tipo para empezar a registrar las habitaciones físicas.',
            'delete_title' => 'Eliminar tipo de habitación',
            'delete_confirm' => '¿Estás seguro de que deseas eliminar “:name”?',
            'delete_disabled' => 'No puedes eliminar este tipo mientras tenga habitaciones asociadas.',
        ],
        'create' => ['heading' => 'Crear tipo de habitación', 'description' => 'Define el nombre y la capacidad que distinguirán esta configuración.'],
        'edit' => ['heading' => 'Editar :name', 'description' => 'Actualiza el nombre o capacidad de esta configuración.'],
    ],
    'form' => ['title' => 'Configuración del tipo', 'description' => 'Estos datos describen una categoría del inventario, no una habitación física.'],
    'messages' => [
        'capacity_blocked' => 'La capacidad no puede ser menor que una asignación activa o confirmada.',
        'created' => 'Tipo de habitación creado con éxito.',
        'updated' => 'Tipo de habitación actualizado con éxito.',
        'deleted' => 'Tipo de habitación eliminado con éxito.',
        'delete_blocked' => 'No puedes eliminar un tipo que todavía tiene habitaciones asociadas.',
    ],
];
