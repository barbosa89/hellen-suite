<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\ActiveSqliteDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActiveSqliteDatabaseTest extends TestCase
{
    #[Test]
    public function it_uses_the_active_connection_and_reports_its_actual_database_file(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'hellen-db-');
        $this->assertNotFalse($path);

        try {
            config()->set('database.connections.backup_test', [
                'driver' => 'sqlite',
                'database' => $path,
                'prefix' => '',
            ]);
            DB::setDefaultConnection('backup_test');
            DB::connection()->statement('CREATE TABLE example (value TEXT)');

            $active = app(ActiveSqliteDatabase::class);

            $this->assertSame(DB::connection()->getPdo(), $active->connection());
            $this->assertSame(realpath($path), realpath($active->path()));
        } finally {
            DB::disconnect('backup_test');
            unlink($path);
        }
    }

    #[Test]
    public function it_rejects_in_memory_databases(): void
    {
        config()->set('database.connections.backup_memory', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        DB::setDefaultConnection('backup_memory');

        $this->expectException(RuntimeException::class);

        app(ActiveSqliteDatabase::class)->path();
    }
}
