<?php

declare(strict_types=1);

return [
    'title' => 'Habitaciones',
    'module_navigation' => 'Navegación del inventario',
    'actions' => ['create' => 'Crear habitación', 'edit' => 'Editar habitación', 'delete' => 'Eliminar habitación'],
    'fields' => [
        'number' => ['label' => 'Número'], 'room_type' => ['label' => 'Tipo'], 'floor' => ['label' => 'Piso'],
        'reference_price' => ['label' => 'Tarifa de referencia'], 'housekeeping_status' => ['label' => 'Estado de limpieza'],
        'is_active' => ['label' => 'Habitación activa'], 'operation' => ['label' => 'Operación'],
    ],
    'housekeeping' => ['clean' => 'Limpia', 'dirty' => 'Pendiente de limpieza'],
    'activity' => ['active' => 'Activa', 'inactive' => 'Inactiva'],
    'pages' => [
        'index' => [
            'heading' => 'Habitaciones de :hotel', 'description' => 'Consulta y actualiza el inventario operativo de :hotel.',
            'inventory_label' => 'Inventario de habitaciones', 'empty_title' => 'Aún no hay habitaciones registradas',
            'empty' => 'Primero crea los tipos que utilizará la propiedad y después registra cada habitación.',
            'currency_required_title' => 'Configura la moneda antes de registrar tarifas',
            'currency_required' => 'La tarifa de referencia necesita una moneda global. Puedes configurarla ahora y regresar al inventario.',
            'delete_title' => 'Eliminar habitación', 'delete_confirm' => '¿Estás seguro de que deseas eliminar la habitación :number?',
        ],
        'create' => ['heading' => 'Crear habitación', 'description' => 'Registra su tipo, ubicación, tarifa y estado operativo inicial.'],
        'edit' => ['heading' => 'Editar habitación :number', 'description' => 'Actualiza los datos administrativos y operativos de esta habitación.'],
    ],
    'form' => [
        'assignment' => ['title' => 'Identificación y ubicación', 'description' => 'Relaciona la habitación con el tipo que corresponde a su configuración física.'],
        'operation' => ['title' => 'Tarifa y operación', 'description' => 'La tarifa es referencial y el estado de limpieza puede actualizarse rápidamente desde el inventario.'],
        'select_type' => 'Selecciona un tipo de habitación', 'reference_price_hint' => 'Guarda hasta dos decimales en la moneda configurada.',
        'activity_hint' => 'Las habitaciones inactivas se conservan en el inventario.',
    ],
    'messages' => [
        'created' => 'Habitación creada con éxito.', 'updated' => 'Habitación actualizada con éxito.',
        'deleted' => 'Habitación eliminada con éxito.', 'activity_updated' => 'El estado de actividad fue actualizado.',
        'housekeeping_updated' => 'El estado de limpieza fue actualizado.',
    ],
];
