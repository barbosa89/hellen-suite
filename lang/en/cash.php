<?php

declare(strict_types=1);

return [
    'title' => 'Cash and shifts',
    'description' => 'Control cash and reconcile each operating period at :hotel.',
    'shift_number' => 'Shift #:number',
    'types' => ['stay_payment' => 'Stay payment', 'payment_refund' => 'Refund', 'manual_entry' => 'Manual entry', 'withdrawal' => 'Withdrawal'],
    'status' => ['open' => 'Open shift', 'closed' => 'Closed shift'],
    'actions' => ['record' => 'Record movement', 'open_shift' => 'Open shift', 'close_shift' => 'Close shift', 'handover' => 'Hand over shift', 'confirm_close' => 'Confirm close', 'confirm_handover' => 'Close and open next', 'back' => 'Back to cash', 'print_a4' => 'Print A4', 'print_thermal' => 'Print 80 mm'],
    'fields' => ['type' => 'Movement type', 'amount' => 'Amount', 'comment' => 'Reason or comment', 'date' => 'Date', 'guest' => 'Guest', 'opening_amount' => 'Opening float', 'opening_note' => 'Opening note', 'closing_note' => 'Closing note', 'next_opening_note' => 'Note for the next shift'],
    'summary' => ['balance' => 'Cash available', 'expected_cash' => 'Expected cash', 'opening' => 'Opening', 'inflows' => 'Inflows', 'outflows' => 'Outflows', 'movements' => 'Shift activity', 'current_shift_only' => 'Only transactions recorded since opening', 'empty' => 'There are no cash movements yet.', 'empty_shift' => 'This shift has no movements yet.', 'automatic' => 'Created automatically from a payment', 'opened_at' => 'Opened :date'],
    'empty' => ['title' => 'The cash desk is ready', 'description' => 'Open a shift with the cash physically counted. From then on, every payment, refund, entry and withdrawal will be reconciled within the operating period.'],
    'open' => ['title' => 'Open a new shift', 'description' => 'Record the physical cash available before operations begin. The float is not treated as revenue.'],
    'close' => ['title' => 'Close shift', 'description' => 'Count and verify each method. The close will be saved and cannot be changed.'],
    'handover' => ['title' => 'Hand over shift', 'description' => 'Close the current period and open the next one using declared cash as its opening float.'],
    'reconciliation' => ['title' => 'Reconciliation preview', 'preview' => 'Expected values by method and currency', 'expected' => 'Expected', 'declared' => 'Counted or verified value', 'difference' => 'Difference', 'irreversible_title' => 'Review before continuing.', 'irreversible' => 'The reconciliation and its differences will be stored as an immutable snapshot.'],
    'history' => ['title' => 'Shift history', 'empty' => 'There are no closed shifts yet.', 'unassigned' => ':count historical movements predate shifts. They have been preserved unchanged.'],
    'messages' => ['recorded' => 'Cash movement recorded successfully.', 'shift_opened' => 'Shift opened successfully.', 'shift_closed' => 'Shift closed and reconciled.', 'shift_handed_over' => 'Shift handed over and next shift opened.'],
    'validation' => ['insufficient_balance' => 'The withdrawal exceeds the cash available in this shift.', 'shift_required' => 'Open a shift before recording financial transactions.', 'shift_already_open' => 'This hotel already has an open shift.', 'shift_closed' => 'This shift is already closed.', 'incomplete_reconciliation' => 'Every method and currency in the shift must be reconciled.', 'negative_cash' => 'Declared cash cannot be negative.'],
    'report' => ['title' => 'Shift close', 'non_fiscal' => 'Operating report. This document is not a fiscal receipt.', 'period' => 'Period', 'method' => 'Method', 'currency' => 'Currency', 'opening' => 'Opening', 'inflows' => 'Inflows', 'outflows' => 'Outflows', 'expected' => 'Expected', 'declared' => 'Declared', 'difference' => 'Difference', 'notes' => 'Notes'],
];
