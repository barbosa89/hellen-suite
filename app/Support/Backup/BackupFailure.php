<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;

class BackupFailure extends RuntimeException
{
    public function __construct(string $message, Throwable $primary, public readonly Throwable $secondary)
    {
        parent::__construct($message, previous: $primary);
    }

    /** @return array{secondary_exception: Throwable} */
    public function context(): array
    {
        return ['secondary_exception' => $this->secondary];
    }
}
