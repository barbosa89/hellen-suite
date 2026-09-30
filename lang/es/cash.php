<?php

declare(strict_types=1);

return [
    'title' => 'Caja',
    'description' => 'Controla únicamente el efectivo disponible en :hotel.',
    'types' => ['stay_payment' => 'Pago de estancia', 'payment_refund' => 'Reembolso', 'manual_entry' => 'Entrada manual', 'withdrawal' => 'Retiro'],
    'actions' => ['record' => 'Registrar movimiento'],
    'fields' => ['type' => 'Tipo de movimiento', 'amount' => 'Importe', 'comment' => 'Motivo o comentario', 'date' => 'Fecha', 'guest' => 'Huésped'],
    'summary' => ['balance' => 'Efectivo disponible', 'movements' => 'Movimientos de caja', 'empty' => 'Aún no hay movimientos de efectivo.', 'automatic' => 'Generado automáticamente por un pago'],
    'messages' => ['recorded' => 'Movimiento de caja registrado con éxito.'],
    'validation' => ['insufficient_balance' => 'El retiro supera el efectivo disponible.'],
];
