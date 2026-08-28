<?php

declare(strict_types=1);

return [
    'title' => 'Estancias',
    'actions' => ['create' => 'Nuevo check-in', 'check_in' => 'Confirmar check-in', 'check_out' => 'Dar salida', 'transfer' => 'Trasladar habitación', 'extend' => 'Cambiar salida esperada', 'add_guest' => 'Agregar huésped'],
    'fields' => ['expected_check_out_on' => ['label' => 'Salida esperada'], 'nightly_rate' => ['label' => 'Tarifa nocturna'], 'room' => ['label' => 'Habitación'], 'guests' => ['label' => 'Huéspedes']],
    'pages' => [
        'index' => ['heading' => 'Estancias', 'description' => 'Gestiona llegadas, huéspedes alojados y salidas de :hotel.', 'empty_title' => 'No hay estancias registradas', 'empty' => 'Inicia un check-in para registrar la primera estancia.'],
        'create' => ['heading' => 'Nuevo check-in', 'description' => 'Registra el grupo, asigna habitaciones disponibles y confirma la estancia.'],
        'show' => [
            'heading' => 'Estancia',
            'occupancies' => 'Habitaciones asignadas',
            'group' => 'Grupo registrado',
            'active' => 'Activa',
            'checked_out' => 'Finalizada',
            'add_guest_description' => 'Busca o registra un acompañante y asígnalo a una habitación con cupo disponible.',
            'no_room_capacity' => 'No hay cupo disponible para agregar otro huésped.',
        ],
    ],
    'form' => [
        'steps' => ['label' => 'Progreso del check-in', 'guests' => 'Huéspedes', 'rooms' => 'Habitaciones', 'review' => 'Confirmación'],
        'actions' => ['back' => 'Atrás', 'continue' => 'Continuar'],
        'stay' => ['check_in_today' => 'Entrada: hoy', 'check_in_hint' => 'La hora de entrada se registra al confirmar el check-in.'],
        'guests' => ['title' => 'Grupo de huéspedes', 'description' => 'Busca una ficha existente o registra a cada persona sin salir del check-in.', 'responsible' => 'Responsable de la estancia', 'companion' => 'Acompañante', 'add_companion' => 'Agregar acompañante', 'new_guest' => 'Registrar nuevo huésped', 'search' => 'Buscar huésped existente'],
        'rooms' => [
            'title' => 'Selecciona habitaciones',
            'description' => 'Elige habitaciones limpias disponibles y asigna a cada huésped de forma explícita.',
            'available_title' => 'Habitaciones disponibles',
            'available_description' => 'Selecciona una o varias habitaciones para esta estancia.',
            'available_count' => ':count disponibles',
            'room_number' => 'Habitación :number',
            'capacity' => ':count huéspedes',
            'night' => 'noche',
            'empty' => 'No hay habitaciones limpias y disponibles en este momento.',
            'selected_title' => 'Habitaciones seleccionadas',
            'selected_description' => 'La tarifa se captura ahora para conservar el valor histórico de esta estancia.',
            'assigned' => 'asignados',
            'remove_room' => 'Quitar habitación :number',
            'assign_title' => 'Asigna cada huésped',
            'assign_description' => 'Cada persona debe ocupar una sola habitación.',
            'assign_label' => 'Habitación asignada',
            'assign_placeholder' => 'Selecciona una habitación',
        ],
        'review' => ['title' => 'Revisión final', 'description' => 'Confirma el grupo y las habitaciones antes de registrar el check-in.', 'guests' => 'Huéspedes', 'rooms' => 'Habitaciones'],
        'summary' => ['title' => 'Resumen de estancia', 'guests' => 'Huéspedes', 'rooms' => 'Habitaciones', 'no_rooms' => 'Aún no hay habitaciones seleccionadas.'],
        'messages' => ['complete_guests' => 'Completa los datos obligatorios de cada huésped antes de continuar.', 'select_check_out' => 'Indica la fecha esperada de salida.', 'select_room' => 'Selecciona al menos una habitación disponible.', 'assign_guests' => 'Asigna cada huésped a una habitación sin exceder su capacidad.'],
    ],
    'messages' => ['checked_in' => 'Check-in registrado con éxito.', 'checked_out' => 'La estancia fue cerrada y las habitaciones quedaron pendientes de limpieza.', 'expected_check_out_updated' => 'La salida esperada fue actualizada.', 'room_transferred' => 'La habitación fue trasladada con éxito.', 'guest_added' => 'El huésped fue agregado a la estancia con éxito.'],
    'validation' => ['responsible_required' => 'Selecciona un huésped responsable válido.', 'unknown_guest' => 'La asignación contiene un huésped que no pertenece al grupo.', 'assign_every_guest_once' => 'Cada huésped debe estar asignado a exactamente una habitación.', 'duplicate_guest' => 'Ya existe un huésped con este documento; búscalo y selecciónalo.', 'room_unavailable' => 'La habitación ya no está disponible para esta estancia.', 'room_capacity' => 'La asignación supera la capacidad de la habitación.', 'stay_closed' => 'La estancia ya se encuentra cerrada.', 'occupancy_closed' => 'La ocupación seleccionada ya se encuentra cerrada.', 'occupancy_invalid' => 'La habitación seleccionada no pertenece a esta estancia.', 'guest_already_in_stay' => 'Este huésped ya está registrado en la estancia.'],
];
