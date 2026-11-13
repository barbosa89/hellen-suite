<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupFailure;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BackupFailureTest extends TestCase
{
    #[Test]
    public function it_exposes_the_secondary_failure_in_laravel_exception_context(): void
    {
        $primary = new RuntimeException('Operation failed.');
        $secondary = new RuntimeException('Cleanup failed.');
        $failure = new BackupFailure('Both failed.', $primary, $secondary);

        $this->assertSame($primary, $failure->getPrevious());
        $this->assertSame(['secondary_exception' => $secondary], $failure->context());
    }
}
