<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\SelectedBackupStager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class SelectedBackupStagerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-selected-backup-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/private');
        file_put_contents($this->directory . '/selected.zip', 'archive contents');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_copies_the_selected_file_into_private_staging(): void
    {
        $result = (new SelectedBackupStager())->stage($this->directory . '/selected.zip', $this->directory . '/private', 100);

        $this->assertSame($this->directory . '/private/selected-backup.zip', $result);
        $this->assertSame('archive contents', file_get_contents($result));
    }

    #[Test]
    public function it_rejects_oversized_files_without_leaving_a_partial_copy(): void
    {
        try {
            (new SelectedBackupStager())->stage($this->directory . '/selected.zip', $this->directory . '/private', 3);
            $this->fail('An oversized archive must not be staged.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/private/selected-backup.zip');
        }
    }

    #[Test]
    public function it_does_not_replace_an_existing_staged_file(): void
    {
        $target = $this->directory . '/private/selected-backup.zip';
        file_put_contents($target, 'existing');

        try {
            (new SelectedBackupStager())->stage($this->directory . '/selected.zip', $this->directory . '/private', 100);
            $this->fail('An existing staged archive must not be replaced.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($target));
        }
    }
}
