<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupDatabaseCompatibility;
use Illuminate\Encryption\Encrypter;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BackupDatabaseCompatibilityTest extends TestCase
{
    #[Test]
    public function it_accepts_matching_schema_version_and_decryptable_credentials(): void
    {
        $encrypter = new Encrypter(str_repeat('a', 32), 'AES-256-CBC');
        $snapshot = $this->database();
        $statement = $snapshot->prepare('INSERT INTO hotel_compliance_profiles (credentials) VALUES (?)');
        $statement->execute([$encrypter->encryptString('{"secret":"example"}')]);

        (new BackupDatabaseCompatibility($encrypter))->verify($snapshot, $this->database(), $this->manifest(), '1.2.3');
        $this->assertTrue(true);
    }

    #[Test]
    public function it_rejects_a_different_application_version(): void
    {
        $this->expectException(RuntimeException::class);

        (new BackupDatabaseCompatibility(new Encrypter(str_repeat('a', 32), 'AES-256-CBC')))
            ->verify($this->database(), $this->database(), $this->manifest(), '1.2.4');
    }

    #[Test]
    public function it_rejects_migrations_that_do_not_match_the_current_database(): void
    {
        $current = $this->database();
        $current->exec("INSERT INTO migrations VALUES ('another', 2)");

        $this->expectException(RuntimeException::class);

        (new BackupDatabaseCompatibility(new Encrypter(str_repeat('a', 32), 'AES-256-CBC')))
            ->verify($this->database(), $current, $this->manifest(), '1.2.3');
    }

    #[Test]
    public function it_rejects_credentials_encrypted_with_a_different_key(): void
    {
        $snapshot = $this->database();
        $differentKey = new Encrypter(str_repeat('b', 32), 'AES-256-CBC');
        $statement = $snapshot->prepare('INSERT INTO hotel_compliance_profiles (credentials) VALUES (?)');
        $statement->execute([$differentKey->encryptString('{"secret":"example"}')]);

        $this->expectException(RuntimeException::class);

        (new BackupDatabaseCompatibility(new Encrypter(str_repeat('a', 32), 'AES-256-CBC')))
            ->verify($snapshot, $this->database(), $this->manifest(), '1.2.3');
    }

    private function database(): PDO
    {
        $database = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        $database->exec("INSERT INTO migrations VALUES ('first', 1)");
        $database->exec('CREATE TABLE hotel_compliance_profiles (credentials TEXT)');

        return $database;
    }

    private function manifest(): array
    {
        return ['format_version' => 1, 'application_version' => '1.2.3', 'migrations' => [['migration' => 'first', 'batch' => 1]]];
    }
}
