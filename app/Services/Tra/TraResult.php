<?php

declare(strict_types=1);

namespace App\Services\Tra;

final readonly class TraResult
{
    public function __construct(
        public bool $sent,
        public int $status,
        public null|string $reference = null,
        public null|string $errorCode = null,
        public null|string $errorMessage = null,
    ) {}

    public static function sent(int $status, null|string $reference = null): self
    {
        return new self(sent: true, status: $status, reference: $reference);
    }

    public static function rejected(int $status, string $errorCode, null|string $errorMessage = null): self
    {
        return new self(sent: false, status: $status, errorCode: $errorCode, errorMessage: $errorMessage);
    }
}
