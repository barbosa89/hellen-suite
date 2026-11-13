<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupManifest;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BackupManifestTest extends TestCase
{
    #[Test]
    public function it_records_sorted_migrations_and_logical_files_in_utc(): void
    {
        $database = new PDO('sqlite::memory:');
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        $database->exec("INSERT INTO migrations VALUES ('second', 2), ('first', 1)");
        $hash = hash('sha256', 'content');

        $manifest = (new BackupManifest())->create($database, '1.2.3', new DateTimeImmutable('2026-11-12T10:00:00-05:00'), [
            'hotels/image.jpg' => ['size' => 7, 'sha256' => $hash],
            'database.sqlite' => ['size' => 7, 'sha256' => $hash],
        ]);

        $this->assertSame([
            'format_version' => 1,
            'created_at' => '2026-11-12T15:00:00Z',
            'application_version' => '1.2.3',
            'migrations' => [
                ['migration' => 'first', 'batch' => 1],
                ['migration' => 'second', 'batch' => 2],
            ],
            'files' => [
                'database.sqlite' => ['size' => 7, 'sha256' => $hash],
                'hotels/image.jpg' => ['size' => 7, 'sha256' => $hash],
            ],
        ], $manifest);
    }

    #[Test]
    public function it_rejects_absolute_or_traversing_archive_names(): void
    {
        $database = new PDO('sqlite::memory:');
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');

        $this->expectException(RuntimeException::class);

        (new BackupManifest())->create($database, '1.2.3', new DateTimeImmutable('2026-11-12T15:00:00Z'), [
            'database.sqlite' => ['size' => 0, 'sha256' => hash('sha256', '')],
            '../secret' => ['size' => 0, 'sha256' => hash('sha256', '')],
        ]);
    }

    #[Test]
    public function it_requires_a_database_snapshot_entry(): void
    {
        $database = new PDO('sqlite::memory:');
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');

        $this->expectException(RuntimeException::class);

        (new BackupManifest())->create($database, '1.2.3', new DateTimeImmutable('2026-11-12T15:00:00Z'), []);
    }
}
