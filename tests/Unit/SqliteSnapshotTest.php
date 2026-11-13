<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\SqliteSnapshot;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class SqliteSnapshotTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'hellen-snapshot-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);

        parent::tearDown();
    }

    #[Test]
    public function it_preserves_the_original_error_when_the_failed_snapshot_is_already_gone(): void
    {
        $path = $this->directory . '/snapshot.sqlite';
        $database = new class($path) extends PDO
        {
            public function __construct(private readonly string $path)
            {
                parent::__construct('sqlite::memory:');
            }

            public function exec(string $statement): int|false
            {
                unlink($this->path);
                throw new \PDOException('Snapshot failed.');
            }
        };

        try {
            (new SqliteSnapshot())->create($database, $path);
            $this->fail('The snapshot must fail.');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(\PDOException::class, $exception);
            $this->assertSame('Snapshot failed.', $exception->getMessage());
            $this->assertFileDoesNotExist($path);
        }
    }

    #[Test]
    public function it_reports_when_a_failed_snapshot_cannot_be_removed(): void
    {
        $path = $this->directory . '/snapshot.sqlite';
        $database = new class($path) extends PDO
        {
            public function __construct(private readonly string $path)
            {
                parent::__construct('sqlite::memory:');
            }

            public function exec(string $statement): int|false
            {
                unlink($this->path);
                mkdir($this->path);
                throw new \PDOException('Snapshot failed.');
            }
        };

        try {
            (new SqliteSnapshot())->create($database, $path);
            $this->fail('The snapshot and its cleanup must both fail.');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertSame('Unable to remove the failed SQLite snapshot.', $exception->getMessage());
            $this->assertSame('Snapshot failed.', $exception->getPrevious()?->getMessage());
        }
    }

    #[Test]
    public function it_creates_an_integral_snapshot_including_committed_wal_changes(): void
    {
        $database = new PDO('sqlite:' . $this->directory . '/live.sqlite');
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $database->exec('PRAGMA journal_mode = WAL');
        $database->exec('CREATE TABLE records (value TEXT NOT NULL)');
        $database->exec("INSERT INTO records VALUES ('before')");

        $path = $this->directory . "/snapshot's.sqlite";
        (new SqliteSnapshot())->create($database, $path);
        $database->exec("INSERT INTO records VALUES ('after')");

        $snapshot = new PDO('sqlite:' . $path);
        $this->assertSame('ok', $snapshot->query('PRAGMA integrity_check')->fetchColumn());
        $this->assertSame(['before'], $snapshot->query('SELECT value FROM records')->fetchAll(PDO::FETCH_COLUMN));
    }

    #[Test]
    public function it_never_replaces_an_existing_file(): void
    {
        $database = new PDO('sqlite::memory:');
        $path = $this->directory . '/existing.sqlite';
        file_put_contents($path, 'existing');

        try {
            (new SqliteSnapshot())->create($database, $path);
            $this->fail('An existing file must not be replaced.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($path));
        }
    }

    #[Test]
    public function it_removes_the_partial_snapshot_if_sqlite_cannot_snapshot(): void
    {
        $database = new PDO('sqlite::memory:');
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $database->beginTransaction();
        $path = $this->directory . '/snapshot.sqlite';

        try {
            (new SqliteSnapshot())->create($database, $path);
            $this->fail('SQLite cannot snapshot inside a transaction.');
        } catch (\PDOException) {
            $this->assertFileDoesNotExist($path);
        } finally {
            $database->rollBack();
        }
    }
}
