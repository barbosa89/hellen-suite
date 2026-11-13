<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\RestoreInstalledVerifier;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreInstalledVerifierTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-installed-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
        mkdir($this->directory . '/hotels');
        mkdir($this->directory . '/payments');
        $database = new PDO('sqlite:' . $this->directory . '/database.sqlite');
        $database->exec('CREATE TABLE records (value TEXT)');
        $database = null;
        file_put_contents($this->directory . '/hotels/photo.jpg', 'photo');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_verifies_sqlite_and_all_installed_files(): void
    {
        $this->assertTrue((new RestoreInstalledVerifier())->verify($this->resources(), $this->files(), $this->manifest()));
    }

    #[Test]
    public function it_rejects_unexpected_old_attachments(): void
    {
        file_put_contents($this->directory . '/hotels/obsolete.jpg', 'old');

        $this->expectException(RuntimeException::class);
        (new RestoreInstalledVerifier())->verify($this->resources(), $this->files(), $this->manifest());
    }

    #[Test]
    public function it_rejects_a_corrupt_sqlite_file_even_if_its_hash_matches(): void
    {
        file_put_contents($this->directory . '/database.sqlite', 'not sqlite');

        $this->expectException(RuntimeException::class);
        (new RestoreInstalledVerifier())->verify($this->resources(), $this->files(), $this->manifest());
    }

    #[Test]
    public function it_rejects_stale_sqlite_wal_files(): void
    {
        file_put_contents($this->directory . '/database.sqlite-wal', 'stale');

        $this->expectException(RuntimeException::class);
        (new RestoreInstalledVerifier())->verify($this->resources(), $this->files(), $this->manifest());
    }

    #[Test]
    public function it_rejects_an_attachment_that_changes_after_staging(): void
    {
        $manifest = $this->manifest();
        file_put_contents($this->directory . '/hotels/photo.jpg', 'other');

        $this->expectException(RuntimeException::class);
        (new RestoreInstalledVerifier())->verify($this->resources(), $this->files(), $manifest);
    }

    #[Test]
    public function it_rejects_a_plan_that_omits_an_attachment_tree_from_verification(): void
    {
        $manifest = $this->manifest();
        unset($manifest['files']['hotels/photo.jpg']);

        $this->expectException(RuntimeException::class);
        (new RestoreInstalledVerifier())->verify($this->resources(), ['payments-public' => []], $manifest);
    }

    private function resources(): array
    {
        return [
            'database' => ['target' => $this->directory . '/database.sqlite'],
            'hotels-public' => ['target' => $this->directory . '/hotels'],
            'payments-public' => ['target' => $this->directory . '/payments'],
        ];
    }

    private function files(): array
    {
        return ['hotels-public' => ['hotels/photo.jpg'], 'payments-public' => []];
    }

    private function manifest(): array
    {
        return ['files' => [
            'database.sqlite' => ['size' => filesize($this->directory . '/database.sqlite'), 'sha256' => hash_file('sha256', $this->directory . '/database.sqlite')],
            'hotels/photo.jpg' => ['size' => 5, 'sha256' => hash('sha256', 'photo')],
        ]];
    }
}
