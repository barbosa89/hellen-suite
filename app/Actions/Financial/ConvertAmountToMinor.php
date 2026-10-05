<?php

declare(strict_types=1);

namespace App\Actions\Financial;

final class ConvertAmountToMinor
{
    public function execute(string $amount): int
    {
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');
        [$whole, $decimal] = array_pad(explode('.', $amount, 2), 2, '0');

        $minor = ((int) $whole * 100) + (int) str_pad(substr($decimal, 0, 2), 2, '0');

        return $negative ? -$minor : $minor;
    }
}
