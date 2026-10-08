<?php

declare(strict_types=1);

namespace App\Services\Tra;

use RuntimeException;

final class TraApiException extends RuntimeException
{
    public static function transport(string $message): self
    {
        return new self($message);
    }
}
