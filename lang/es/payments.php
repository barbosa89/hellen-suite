<?php

declare(strict_types=1);

return [
    'title' => 'Folio y pagos',
    'folio' => ['room' => 'Habitación :room'],
    'charges' => ['lodging' => 'Hospedaje'],
    'methods' => ['cash' => 'Efectivo', 'bank_transfer' => 'Transferencia'],
    'adjustments' => ['courtesy' => 'Cortesía', 'write_off' => 'Pérdida', 'discount' => 'Descuento'],
    'actions' => ['record_payment' => 'Registrar pago', 'add_charge' => 'Agregar cargo', 'apply_adjustment' => 'Aplicar ajuste', 'refund' => 'Reembolsar'],
    'fields' => ['folio' => 'Folio', 'amount' => 'Importe', 'method' => 'Método', 'comment' => 'Comentario', 'support' => 'Soporte de transferencia', 'support_hint' => 'JPG, PNG o WebP · máximo 5 MB', 'description' => 'Descripción', 'type' => 'Tipo', 'reason' => 'Motivo'],
    'summary' => ['description' => 'Cargos, ajustes y dinero recibido por cada habitación.', 'charges' => 'Cargos', 'adjustments' => 'Ajustes', 'paid' => 'Pagado', 'balance' => 'Saldo por liquidar', 'settled' => 'Liquidado', 'projected' => 'Incluye hospedaje proyectado', 'empty' => 'Todavía no hay movimientos en este folio.', 'history' => 'Movimientos', 'additional_charges' => 'Cargos adicionales de la habitación', 'no_additional_charges' => 'No se han agregado cargos adicionales a esta habitación.'],
    'dialogs' => ['payment_title' => 'Registrar pago', 'payment_description' => 'El pago se aplicará al folio seleccionado.', 'charge_title' => 'Agregar cargo manual', 'adjustment_title' => 'Aplicar cortesía o pérdida'],
    'messages' => ['recorded' => 'Pago registrado con éxito.', 'charge_recorded' => 'Cargo registrado con éxito.', 'adjustment_recorded' => 'Ajuste registrado con éxito.', 'refunded' => 'Reembolso registrado con éxito.'],
    'validation' => ['balance_due' => 'El folio debe quedar con saldo cero antes del checkout.', 'folio_closed' => 'El folio ya está cerrado y no admite nuevos movimientos.', 'overpayment' => 'El pago no puede superar el saldo del folio.', 'over_adjustment' => 'El ajuste no puede superar el saldo del folio.', 'refund_exceeds_payment' => 'El reembolso supera el importe disponible del pago.'],
];
