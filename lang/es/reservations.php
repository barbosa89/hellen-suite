<?php

declare(strict_types=1);

return [
    'title' => 'Reservas',
    'statuses' => ['draft' => 'Borrador', 'confirmed' => 'Confirmada', 'checked_in' => 'Ingresada', 'cancelled' => 'Cancelada', 'no_show' => 'No-show'],
    'actions' => ['create' => 'Nueva reserva', 'view' => 'Ver reserva', 'edit' => 'Modificar', 'confirm' => 'Confirmar reserva', 'cancel' => 'Cancelar reserva', 'no_show' => 'Marcar no-show', 'check_in' => 'Hacer check-in', 'save_draft' => 'Guardar borrador', 'continue' => 'Continuar', 'back' => 'Atrás'],
    'fields' => ['check_in' => 'Llegada planeada', 'check_out' => 'Salida planeada', 'rate' => 'Tarifa por noche', 'nights' => 'Noches', 'guests' => 'Huéspedes', 'rooms' => 'Habitaciones', 'quote' => 'Cotización acordada'],
    'pages' => [
        'index' => ['description' => 'Compromisos futuros, llegadas y reservas históricas de :hotel.', 'search' => 'Buscar por huésped o identificación', 'all_statuses' => 'Todos los estados', 'empty_title' => 'Todavía no hay reservas', 'empty' => 'Crea un borrador, asigna el grupo y confirma sólo cuando las condiciones estén acordadas.'],
        'create' => ['heading' => 'Crear reserva', 'description' => 'Prepara fechas, grupo, habitaciones concretas y cotización antes de confirmar el compromiso.'],
        'edit' => ['heading' => 'Modificar reserva', 'description' => 'Los cambios se registran y vuelven a comprobar la disponibilidad completa.'],
        'show' => ['heading' => 'Reserva #:id', 'plan' => 'Plan reservado', 'quote_note' => 'Esta cotización permanece como contexto histórico aunque cambie el precio de referencia.', 'reserved_plan' => 'Plan original de la reserva', 'current_stay' => 'Estancia vigente', 'open_stay' => 'Abrir estancia', 'rooms' => 'Habitaciones y huéspedes', 'history' => 'Historial de reserva'],
    ],
    'form' => [
        'steps' => ['label' => 'Pasos de la reserva', 'dates' => 'Fechas', 'guests' => 'Grupo', 'rooms' => 'Habitaciones', 'review' => 'Revisión'],
        'dates' => ['title' => 'Define el intervalo', 'description' => 'La salida no consume noche y puede coincidir con la llegada de otro grupo.'],
        'guests' => ['title' => 'Registra el grupo', 'description' => 'Busca huéspedes existentes antes de crear un registro nuevo.', 'responsible' => 'Huésped responsable', 'companion' => 'Acompañante', 'unnamed' => 'Huésped sin nombre', 'new' => 'Registrar otro', 'add' => 'Agregar acompañante'],
        'rooms' => ['title' => 'Reserva habitaciones concretas', 'description' => 'Sólo se muestran habitaciones vendibles y libres durante todo el intervalo.', 'loading' => 'Comprobando disponibilidad...', 'empty' => 'No hay habitaciones disponibles para estas fechas.', 'number' => 'Habitación :number', 'capacity' => 'capacidad :count', 'night' => 'noche', 'assigned' => 'asignados', 'assign' => 'Distribuye cada huésped', 'choose' => 'Elegir habitación', 'principal_label' => 'Huésped principal (titular TRA)'],
        'review' => ['title' => 'Revisa el compromiso', 'description' => 'Confirma fechas, distribución y tarifa antes de guardar.', 'draft_note' => 'La reserva se guardará como borrador y no bloqueará inventario hasta que la confirmes.'],
        'summary' => ['title' => 'Resumen de reserva'],
        'errors' => ['dates' => 'Selecciona un intervalo válido.', 'guests' => 'Completa los datos obligatorios de cada huésped.', 'rooms' => 'Selecciona habitaciones y asigna cada huésped exactamente una vez.', 'principal' => 'Selecciona el huésped principal de cada habitación para el reporte TRA.'],
    ],
    'events' => ['created' => 'Reserva creada', 'updated' => 'Reserva modificada', 'confirmed' => 'Reserva confirmada', 'cancelled' => 'Reserva cancelada', 'no_show' => 'Marcada como no-show', 'checked_in' => 'Check-in registrado'],
    'dialogs' => [
        'confirm' => ['title' => 'Confirmar reserva', 'description' => 'La confirmación bloqueará las habitaciones durante el intervalo planeado.', 'action' => 'Confirmar'],
        'cancel' => ['title' => 'Cancelar reserva', 'description' => 'Las habitaciones dejarán de estar comprometidas. El historial se conservará.', 'action' => 'Cancelar reserva'],
        'no-show' => ['title' => 'Marcar no-show', 'description' => 'Registra que el grupo no llegó y libera las habitaciones sin crear una estancia.', 'action' => 'Marcar no-show'],
        'check-in' => ['title' => 'Hacer check-in', 'description' => 'Se creará una estancia con el grupo, las habitaciones y las tarifas acordadas.', 'action' => 'Crear estancia'],
    ],
    'validation' => ['room_unavailable' => 'Una o más habitaciones ya no están disponibles para el intervalo seleccionado.', 'room_unavailable_at_check_in' => 'Una o más habitaciones no están limpias o disponibles para hacer check-in.', 'room_capacity' => 'La asignación supera la capacidad de la habitación.', 'responsible_required' => 'Selecciona un huésped responsable válido.', 'unknown_guest' => 'La asignación contiene un huésped desconocido.', 'assign_every_guest_once' => 'Cada huésped debe estar asignado exactamente a una habitación.', 'duplicate_guest' => 'Ya existe un huésped con esta identificación. Búscalo y selecciónalo.', 'immutable' => 'Esta reserva ya no se puede modificar.', 'cannot_confirm' => 'Sólo un borrador con llegada vigente puede confirmarse.', 'cannot_cancel' => 'Esta reserva ya no se puede cancelar.', 'cannot_mark_no_show' => 'Sólo una reserva confirmada cuya llegada ya venció puede marcarse no-show.', 'cannot_check_in' => 'La reserva todavía no admite check-in o ya fue procesada.', 'tra_identity_required' => 'Este dato es obligatorio para el reporte TRA.', 'tra_guest_incomplete' => 'Completa los datos legales de este huésped (nacimiento, género y nacionalidad) en su ficha.', 'tra_travel_required' => 'Este dato de viaje es obligatorio para el reporte TRA.', 'tra_geo_required' => 'Selecciona departamento y municipio válidos de Colombia.', 'tra_geo_invalid' => 'El municipio no pertenece al departamento seleccionado.', 'tra_geo_foreign' => 'Departamento y municipio solo aplican cuando el país es Colombia.', 'principal_required' => 'Selecciona el huésped principal (titular TRA) de la habitación.', 'principal_invalid' => 'El principal debe ser uno de los huéspedes asignados a la habitación.'],
    'messages' => ['created' => 'Reserva guardada como borrador.', 'updated' => 'Reserva actualizada.', 'confirmed' => 'Reserva confirmada; las habitaciones quedaron protegidas.', 'cancelled' => 'Reserva cancelada.', 'no_show' => 'Reserva marcada como no-show.', 'checked_in' => 'Check-in creado desde la reserva.'],
];
