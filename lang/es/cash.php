<?php

declare(strict_types=1);

return [
    'title' => 'Caja y turnos',
    'description' => 'Controla el efectivo y concilia cada jornada operativa de :hotel.',
    'shift_number' => 'Turno #:number',
    'types' => ['stay_payment' => 'Pago de estancia', 'payment_refund' => 'Reembolso', 'manual_entry' => 'Entrada manual', 'withdrawal' => 'Retiro'],
    'status' => ['open' => 'Turno abierto', 'closed' => 'Turno cerrado'],
    'actions' => [
        'record' => 'Registrar movimiento',
        'open_shift' => 'Abrir turno',
        'close_shift' => 'Cerrar turno',
        'handover' => 'Entregar turno',
        'confirm_close' => 'Confirmar cierre',
        'confirm_handover' => 'Cerrar y abrir siguiente',
        'back' => 'Volver a caja',
        'print_a4' => 'Imprimir A4',
        'print_thermal' => 'Imprimir 80 mm',
    ],
    'fields' => [
        'type' => 'Tipo de movimiento', 'amount' => 'Importe', 'comment' => 'Motivo o comentario', 'date' => 'Fecha', 'guest' => 'Huésped',
        'opening_amount' => 'Base inicial', 'opening_note' => 'Nota de apertura', 'closing_note' => 'Nota de cierre', 'next_opening_note' => 'Nota para el siguiente turno',
    ],
    'summary' => [
        'balance' => 'Efectivo disponible', 'expected_cash' => 'Efectivo esperado', 'opening' => 'Base', 'inflows' => 'Entradas', 'outflows' => 'Salidas',
        'movements' => 'Actividad del turno', 'current_shift_only' => 'Solo operaciones registradas desde la apertura', 'empty' => 'Aún no hay movimientos de efectivo.',
        'empty_shift' => 'Este turno todavía no tiene movimientos.', 'automatic' => 'Generado automáticamente por un pago', 'opened_at' => 'Abierto :date',
    ],
    'empty' => ['title' => 'La caja está lista para comenzar', 'description' => 'Abre un turno con el efectivo contado en caja. A partir de ese momento, cada pago, devolución, entrada y retiro quedará conciliado dentro de la jornada.'],
    'open' => ['title' => 'Abrir un nuevo turno', 'description' => 'Registra el efectivo físico disponible antes de iniciar operaciones. La base no cuenta como un ingreso.'],
    'close' => ['title' => 'Cerrar turno', 'description' => 'Cuenta y verifica cada método. El cierre quedará guardado y no podrá modificarse.'],
    'handover' => ['title' => 'Entregar turno', 'description' => 'Cierra la jornada actual y abre la siguiente usando el efectivo declarado como nueva base.'],
    'reconciliation' => [
        'title' => 'Vista de conciliación', 'preview' => 'Valores esperados por método y moneda', 'expected' => 'Esperado', 'declared' => 'Valor contado o verificado', 'difference' => 'Diferencia',
        'irreversible_title' => 'Revisa antes de continuar.', 'irreversible' => 'El arqueo y sus diferencias quedarán como una instantánea inmutable.',
    ],
    'history' => ['title' => 'Historial de turnos', 'empty' => 'Todavía no hay turnos cerrados.', 'unassigned' => 'Hay :count movimientos históricos anteriores al uso de turnos. Se conservan sin modificar.'],
    'messages' => [
        'recorded' => 'Movimiento de caja registrado con éxito.', 'shift_opened' => 'Turno abierto con éxito.', 'shift_closed' => 'Turno cerrado y conciliado.', 'shift_handed_over' => 'Turno entregado y siguiente turno abierto.',
    ],
    'validation' => [
        'insufficient_balance' => 'El retiro supera el efectivo disponible en este turno.', 'shift_required' => 'Abre un turno antes de registrar operaciones financieras.',
        'shift_already_open' => 'Ya existe un turno abierto para este hotel.', 'shift_closed' => 'Este turno ya está cerrado.', 'incomplete_reconciliation' => 'Debes conciliar todos los métodos y monedas del turno.',
        'negative_cash' => 'El efectivo declarado no puede ser negativo.',
    ],
    'report' => ['title' => 'Cierre de turno', 'non_fiscal' => 'Reporte operativo. Este documento no es un comprobante fiscal.', 'period' => 'Periodo', 'method' => 'Método', 'currency' => 'Moneda', 'opening' => 'Base', 'inflows' => 'Entradas', 'outflows' => 'Salidas', 'expected' => 'Esperado', 'declared' => 'Declarado', 'difference' => 'Diferencia', 'notes' => 'Notas'],
];
