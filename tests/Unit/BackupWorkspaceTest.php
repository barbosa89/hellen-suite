<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupWorkspace;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BackupWorkspaceTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-workspace-test-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_creates_distinct_workspaces_and_removes_only_the_selected_workspace(): void
    {
        $first = BackupWorkspace::create($this->directory);
        $second = BackupWorkspace::create($this->directory);
        file_put_contents($first->path() . '/sensitive', 'private');
        file_put_contents($second->path() . '/other', 'untouched');
        $firstPath = $first->path();

        $this->assertNotSame($firstPath, $second->path());
        $first->remove();

        $this->assertDirectoryDoesNotExist($firstPath);
        $this->assertSame('untouched', file_get_contents($second->path() . '/other'));
        $second->remove();
    }

    #[Test]
    public function it_rejects_an_unavailable_parent_directory(): void
    {
        $this->expectException(RuntimeException::class);

        BackupWorkspace::create($this->directory . '/missing');
    }

    #[Test]
    public function it_does_not_allow_reusing_a_removed_workspace(): void
    {
        $workspace = BackupWorkspace::create($this->directory);
        $workspace->remove();

        $this->expectException(RuntimeException::class);

        $workspace->path();
    }

    #[Test]
    public function it_restricts_workspace_permissions_on_posix(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('POSIX permission bits do not establish Windows ACLs.');
        }

        $workspace = BackupWorkspace::create($this->directory);
        $this->assertSame(0700, fileperms($workspace->path()) & 0777);
        $workspace->remove();
    }
}
