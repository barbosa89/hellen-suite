<?php

declare(strict_types=1);

return [
    'title' => 'Cash',
    'description' => 'Track only the physical cash available at :hotel.',
    'types' => ['stay_payment' => 'Stay payment', 'payment_refund' => 'Refund', 'manual_entry' => 'Manual entry', 'withdrawal' => 'Withdrawal'],
    'actions' => ['record' => 'Record movement'],
    'fields' => ['type' => 'Movement type', 'amount' => 'Amount', 'comment' => 'Reason or comment', 'date' => 'Date', 'guest' => 'Guest'],
    'summary' => ['balance' => 'Cash available', 'movements' => 'Cash movements', 'empty' => 'There are no cash movements yet.', 'automatic' => 'Created automatically from a payment'],
    'messages' => ['recorded' => 'Cash movement recorded successfully.'],
    'validation' => ['insufficient_balance' => 'The withdrawal exceeds the available cash.'],
];
