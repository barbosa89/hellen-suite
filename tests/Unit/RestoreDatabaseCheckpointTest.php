<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\RestoreDatabaseCheckpoint;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

class RestoreDatabaseCheckpointTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-checkpoint-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_folds_committed_transactions_into_the_database_file(): void
    {
        $path = $this->directory . '/live.sqlite';
        $database = new PDO('sqlite:' . $path);
        $database->exec('PRAGMA journal_mode = WAL');
        $database->exec('CREATE TABLE records (value TEXT)');
        $database->exec("INSERT INTO records VALUES ('committed')");
        $this->assertFileExists($path . '-wal');

        (new RestoreDatabaseCheckpoint())->prepare($path);

        $this->assertFileDoesNotExist($path . '-wal');
        $this->assertFileDoesNotExist($path . '-shm');
        $database = new PDO('sqlite:' . $path);
        $this->assertSame('committed', $database->query('SELECT value FROM records')->fetchColumn());
    }

    #[Test]
    public function it_rejects_a_database_that_cannot_be_opened(): void
    {
        $this->expectException(RuntimeException::class);

        (new RestoreDatabaseCheckpoint())->prepare($this->directory . '/missing.sqlite');
    }

    #[Test]
    public function it_rejects_a_database_with_unreadable_content(): void
    {
        $path = $this->directory . '/broken.sqlite';
        file_put_contents($path, 'not a database');

        $this->expectException(RuntimeException::class);

        (new RestoreDatabaseCheckpoint())->prepare($path);
    }
}
