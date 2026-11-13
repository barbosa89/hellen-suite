<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreRecovery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreRecoveryInterruptionTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-interruption-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    #[DataProvider('interruptionPoints')]
    public function recovery_returns_the_previous_resource_at_each_interruption_point(string $point): void
    {
        $paths = [
            'target' => $this->directory . '/live.sqlite',
            'previous' => $this->directory . '/previous.sqlite',
            'replacement' => $this->directory . '/replacement.sqlite',
            'existed' => true,
        ];
        $resources = ['database' => $paths];
        $journalPath = $this->directory . '/restore.journal';
        file_put_contents($paths['target'], 'old');
        file_put_contents($paths['replacement'], 'new');
        $journal = new RestoreJournal();
        $journal->create($journalPath, 'transaction-1', $resources);
        $steps = [
            'preserve_started' => fn () => $journal->append($journalPath, 'preserve_started', 'database'),
            'old_moved' => fn () => rename($paths['target'], $paths['previous']),
            'preserve_completed' => fn () => $journal->append($journalPath, 'preserve_completed', 'database'),
            'install_started' => fn () => $journal->append($journalPath, 'install_started', 'database'),
            'new_moved' => fn () => rename($paths['replacement'], $paths['target']),
            'install_completed' => fn () => $journal->append($journalPath, 'install_completed', 'database'),
            'verify_completed' => fn () => $journal->append($journalPath, 'verify_completed', 'database'),
            'rollback_started' => fn () => $journal->append($journalPath, 'rollback_started', 'database'),
            'new_moved_back' => fn () => rename($paths['target'], $paths['replacement']),
            'old_restored' => fn () => rename($paths['previous'], $paths['target']),
            'rollback_completed' => fn () => $journal->append($journalPath, 'rollback_completed', 'database'),
        ];

        if ($point !== 'prepared') {
            foreach ($steps as $name => $step) {
                $step();

                if ($name === $point) {
                    break;
                }
            }
        }

        (new RestoreRecovery($journal))->rollback($journalPath, $resources);

        $this->assertSame('old', file_get_contents($paths['target']));
        $this->assertSame('new', file_get_contents($paths['replacement']));
        $this->assertFileDoesNotExist($paths['previous']);
        $this->assertFileExists($journalPath);
    }

    public static function interruptionPoints(): array
    {
        return array_map(fn (string $point): array => [$point], [
            'prepared', 'preserve_started', 'old_moved', 'preserve_completed', 'install_started', 'new_moved',
            'install_completed', 'verify_completed', 'rollback_started', 'new_moved_back', 'old_restored', 'rollback_completed',
        ]);
    }
}
