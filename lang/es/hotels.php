<?php

declare(strict_types=1);

return [
    'title' => 'Hoteles',
    'directory' => 'Directorio de hoteles',
    'selected_hotel' => 'Hotel seleccionado',

    'actions' => [
        'view' => 'Ver hotel',
        'edit' => 'Editar hotel',
        'delete' => 'Eliminar hotel',
        'create' => 'Crear hotel',
        'manage' => 'Administrar',
        'open' => 'Abrir hotel',
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
            'description' => 'Administra tus propiedades y entra al espacio de trabajo de cada hotel.',
            'directory_label' => 'Directorio de hoteles registrados',
            'registered' => 'hotel registrado|hoteles registrados',
            'contact' => 'Contacto',
            'hotel_identifier' => 'Hotel #:id',
            'open_actions' => 'Abrir acciones para :name',
            'no_hotels' => 'No hay hoteles registrados aún',
            'no_hotels_hint' => 'Agrega tu primer hotel para mantener el registro actualizado.',
            'delete_title' => 'Eliminar hotel',
            'delete_confirm' => '¿Estás seguro de que deseas eliminar ":name"?',
        ],
        'create' => [
            'heading' => 'Crear hotel',
            'description' => 'Registra la identidad, ubicación y datos de contacto de la nueva propiedad.',
        ],
        'edit' => [
            'heading' => 'Editar hotel',
            'description' => 'Actualiza la información registrada para :name.',
        ],
        'show' => [
            'description' => 'Consulta la información legal y de contacto registrada para esta propiedad.',
            'no_image' => 'Este hotel aún no tiene una imagen registrada.',
        ],
    ],

    'form' => [
        'identity' => [
            'title' => 'Identidad del hotel',
            'description' => 'Estos datos identifican legal y comercialmente la propiedad.',
        ],
        'contact' => [
            'title' => 'Ubicación y contacto',
            'description' => 'Mantén disponibles los canales que utiliza el equipo para atender la operación.',
        ],
        'image' => [
            'title' => 'Imagen de la propiedad',
            'description' => 'Usa una fotografía reconocible para encontrar el hotel rápidamente en el directorio.',
        ],
    ],

    'management' => [
        'heading' => 'Resumen del hotel',
        'description' => 'Una vista clara de la operación diaria de :name.',
        'contact_phone' => 'Teléfono de contacto',
        'profile_status' => 'Estado del perfil',
        'profile_progress' => ':completed de :total datos principales registrados.',
        'complete_profile' => 'Completar información',
        'metrics' => [
            'eyebrow' => 'Pulso operativo',
            'title' => 'Lo que está pasando en el hotel',
            'description' => 'Indicadores actuales y demanda confirmada para tomar decisiones con rapidez.',
            'current_occupancy' => 'Ocupación actual',
            'rooms_occupied' => ':occupied de :total habitaciones activas ocupadas',
            'adr' => 'ADR actual',
            'revpar' => 'RevPAR actual',
            'available_rooms' => 'Habitaciones disponibles',
            'arrivals_today' => 'Llegadas previstas hoy',
            'departures_today' => 'Salidas previstas hoy',
            'guests_in_house' => 'Huéspedes alojados',
            'dirty_rooms' => 'Habitaciones por limpiar',
            'outstanding_balance' => 'Saldo pendiente registrado',
            'posted_balance_note' => 'Incluye cargos, ajustes, pagos y reembolsos ya registrados.',
            'currency_unavailable' => 'Moneda sin configurar',
        ],
        'operations' => [
            'title' => 'Operación de hoy',
            'description' => 'Movimientos y tareas que requieren atención inmediata.',
        ],
        'forecast' => [
            'title' => 'Proyección de ocupación',
            'description' => 'Estadías activas y reservas confirmadas para los próximos 7 días.',
            'tooltip' => ':rate% de ocupación',
            'accessible_point' => ':date: :rate% de ocupación.',
        ],
    ],

    'messages' => [
        'created' => 'Hotel creado con éxito.',
        'updated' => 'Hotel actualizado con éxito.',
        'deleted' => 'Hotel eliminado con éxito.',
    ],

];
