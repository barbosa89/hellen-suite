<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupAttachmentReferences;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BackupAttachmentReferencesTest extends TestCase
{
    #[Test]
    public function it_reads_raw_references_from_the_snapshot_and_normalizes_windows_paths(): void
    {
        $snapshot = $this->database();
        $snapshot->exec("INSERT INTO hotels (image) VALUES ('hotels/one.jpg'), ('hotels\\two.jpg'), (NULL)");
        $snapshot->exec("INSERT INTO payments (support_path) VALUES ('payments/receipt.pdf'), ('payments/receipt.pdf'), (NULL)");

        $this->assertSame([
            'hotels' => ['hotels/one.jpg', 'hotels/two.jpg'],
            'payments' => ['payments/receipt.pdf'],
        ], (new BackupAttachmentReferences())->read($snapshot));
    }

    #[Test]
    public function it_rejects_a_reference_outside_the_expected_tree(): void
    {
        $snapshot = $this->database();
        $snapshot->exec("INSERT INTO payments (support_path) VALUES ('payments/../secret')");

        $this->expectException(RuntimeException::class);
        (new BackupAttachmentReferences())->read($snapshot);
    }

    private function database(): PDO
    {
        $database = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $database->exec('CREATE TABLE hotels (image TEXT)');
        $database->exec('CREATE TABLE payments (support_path TEXT)');

        return $database;
    }
}
