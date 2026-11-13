<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\BackupMigrationReader;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BackupMigrationReaderTest extends TestCase
{
    #[Test]
    public function it_returns_sorted_migrations_with_integer_batches(): void
    {
        $database = new PDO('sqlite::memory:');
        $database->exec('CREATE TABLE migrations (migration TEXT, batch TEXT)');
        $database->exec("INSERT INTO migrations VALUES ('z', '2'), ('a', '1')");

        $this->assertSame([
            ['migration' => 'a', 'batch' => 1],
            ['migration' => 'z', 'batch' => 2],
        ], (new BackupMigrationReader())->read($database));
    }
}
