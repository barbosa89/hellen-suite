<?php

declare(strict_types=1);

return [
    'title' => 'Folio and payments',
    'folio' => ['room' => 'Room :room'],
    'charges' => ['lodging' => 'Lodging'],
    'methods' => ['cash' => 'Cash', 'bank_transfer' => 'Bank transfer'],
    'adjustments' => ['courtesy' => 'Courtesy', 'write_off' => 'Write-off', 'discount' => 'Discount'],
    'actions' => ['record_payment' => 'Record payment', 'add_charge' => 'Add charge', 'apply_adjustment' => 'Apply adjustment', 'refund' => 'Refund'],
    'fields' => ['folio' => 'Folio', 'amount' => 'Amount', 'method' => 'Method', 'comment' => 'Comment', 'support' => 'Transfer evidence', 'support_hint' => 'JPG, PNG, or WebP · maximum 5 MB', 'description' => 'Description', 'type' => 'Type', 'reason' => 'Reason'],
    'summary' => ['description' => 'Charges, adjustments, and money received for each room.', 'charges' => 'Charges', 'adjustments' => 'Adjustments', 'paid' => 'Paid', 'balance' => 'Balance due', 'settled' => 'Settled', 'projected' => 'Includes projected lodging', 'empty' => 'There are no entries in this folio yet.', 'history' => 'Transactions', 'additional_charges' => 'Additional room charges', 'no_additional_charges' => 'No additional charges have been added to this room.'],
    'dialogs' => ['payment_title' => 'Record payment', 'payment_description' => 'The payment will be applied to the selected folio.', 'charge_title' => 'Add manual charge', 'adjustment_title' => 'Apply courtesy or write-off'],
    'messages' => ['recorded' => 'Payment recorded successfully.', 'charge_recorded' => 'Charge recorded successfully.', 'adjustment_recorded' => 'Adjustment recorded successfully.', 'refunded' => 'Refund recorded successfully.'],
    'validation' => ['balance_due' => 'The folio must have a zero balance before checkout.', 'folio_closed' => 'The folio is closed and cannot accept new entries.', 'overpayment' => 'The payment cannot exceed the folio balance.', 'over_adjustment' => 'The adjustment cannot exceed the folio balance.', 'refund_exceeds_payment' => 'The refund exceeds the available payment amount.'],
];
