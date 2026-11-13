<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreRecovery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreRecoveryTest extends TestCase
{
    private string $directory;

    private string $journalPath;

    private array $resources;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-recovery-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        $this->journalPath = $this->directory . '/restore.journal';
        $this->resources = [
            'database' => [
                'target' => $this->directory . '/live.sqlite',
                'previous' => $this->directory . '/previous.sqlite',
                'replacement' => $this->directory . '/replacement.sqlite',
                'existed' => true,
            ],
        ];
        file_put_contents($this->resources['database']['target'], 'old');
        file_put_contents($this->resources['database']['replacement'], 'new');
        (new RestoreJournal())->create($this->journalPath, 'transaction-1', $this->resources);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_rolls_back_after_the_new_resource_was_installed(): void
    {
        $this->installReplacement();

        (new RestoreRecovery(new RestoreJournal()))->rollback($this->journalPath, $this->resources);

        $this->assertSame('old', file_get_contents($this->resources['database']['target']));
        $this->assertSame('new', file_get_contents($this->resources['database']['replacement']));
        $this->assertFileDoesNotExist($this->resources['database']['previous']);
        $this->assertSame('rollback_completed', end((new RestoreJournal())->read($this->journalPath)['events'])['event']);
        $this->assertFileExists($this->journalPath);
    }

    #[Test]
    public function it_resumes_a_rollback_interrupted_after_the_new_resource_was_moved(): void
    {
        $this->installReplacement();
        (new RestoreJournal())->append($this->journalPath, 'rollback_started', 'database');
        rename($this->resources['database']['target'], $this->resources['database']['replacement']);

        (new RestoreRecovery(new RestoreJournal()))->rollback($this->journalPath, $this->resources);

        $this->assertSame('old', file_get_contents($this->resources['database']['target']));
        $this->assertSame('new', file_get_contents($this->resources['database']['replacement']));
    }

    #[Test]
    public function it_rejects_journal_paths_that_do_not_match_the_trusted_plan(): void
    {
        $untrusted = $this->resources;
        $untrusted['database']['target'] = $this->directory . '/other.sqlite';

        $this->expectException(RuntimeException::class);
        (new RestoreRecovery(new RestoreJournal()))->rollback($this->journalPath, $untrusted);
    }

    #[Test]
    public function it_refuses_ambiguous_states_without_overwriting_any_file(): void
    {
        $journal = new RestoreJournal();
        $journal->append($this->journalPath, 'preserve_started', 'database');
        file_put_contents($this->resources['database']['previous'], 'other');

        try {
            (new RestoreRecovery($journal))->rollback($this->journalPath, $this->resources);
            $this->fail('Ambiguous resource states must not be changed.');
        } catch (RuntimeException) {
            $this->assertSame('old', file_get_contents($this->resources['database']['target']));
            $this->assertSame('other', file_get_contents($this->resources['database']['previous']));
        }
    }

    #[Test]
    public function it_recovers_a_resource_that_did_not_exist_before_restoration(): void
    {
        $resources = [
            'payments' => [
                'target' => $this->directory . '/payments',
                'previous' => $this->directory . '/previous-payments',
                'replacement' => $this->directory . '/replacement-payments',
                'existed' => false,
            ],
        ];
        $journalPath = $this->directory . '/other.journal';
        mkdir($resources['payments']['replacement']);
        file_put_contents($resources['payments']['replacement'] . '/receipt', 'new');
        $journal = new RestoreJournal();
        $journal->create($journalPath, 'transaction-2', $resources);
        $journal->append($journalPath, 'preserve_started', 'payments');
        $journal->append($journalPath, 'preserve_completed', 'payments');
        $journal->append($journalPath, 'install_started', 'payments');
        rename($resources['payments']['replacement'], $resources['payments']['target']);

        $recovery = new RestoreRecovery($journal);
        $recovery->rollback($journalPath, $resources);
        $recovery->rollback($journalPath, $resources);

        $this->assertDirectoryDoesNotExist($resources['payments']['target']);
        $this->assertSame('new', file_get_contents($resources['payments']['replacement'] . '/receipt'));
    }

    #[Test]
    public function it_checks_all_resources_before_reverting_any_of_them(): void
    {
        $resources = [
            'database' => [
                'target' => $this->directory . '/db-live',
                'previous' => $this->directory . '/db-old',
                'replacement' => $this->directory . '/db-new',
                'existed' => true,
            ],
            'hotels' => [
                'target' => $this->directory . '/hotels-live',
                'previous' => $this->directory . '/hotels-old',
                'replacement' => $this->directory . '/hotels-new',
                'existed' => true,
            ],
        ];
        $path = $this->directory . '/multiple.journal';
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-3', $resources);
        file_put_contents($resources['database']['previous'], 'old database');
        file_put_contents($resources['database']['target'], 'new database');
        $journal->append($path, 'preserve_started', 'database');
        $journal->append($path, 'preserve_completed', 'database');
        $journal->append($path, 'install_started', 'database');
        $journal->append($path, 'install_completed', 'database');
        file_put_contents($resources['hotels']['target'], 'old hotels');
        file_put_contents($resources['hotels']['previous'], 'ambiguous hotels');
        file_put_contents($resources['hotels']['replacement'], 'new hotels');
        $journal->append($path, 'preserve_started', 'hotels');

        try {
            (new RestoreRecovery($journal))->rollback($path, $resources);
            $this->fail('Ambiguous resources must stop the entire recovery.');
        } catch (RuntimeException) {
            $this->assertSame('new database', file_get_contents($resources['database']['target']));
            $this->assertSame('old database', file_get_contents($resources['database']['previous']));
        }
    }

    #[Test]
    public function it_does_not_mark_a_rollback_complete_if_directory_sync_fails(): void
    {
        $real = $this->directory . '/real';
        $link = $this->directory . '/linked';
        mkdir($real);

        if (! @symlink($real, $link)) {
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        $resources = ['database' => [
            'target' => $link . '/live.sqlite',
            'previous' => $link . '/old.sqlite',
            'replacement' => $link . '/new.sqlite',
            'existed' => true,
        ]];
        $path = $this->directory . '/sync-failure.journal';
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-4', $resources);
        file_put_contents($resources['database']['previous'], 'old');
        file_put_contents($resources['database']['target'], 'new');
        $journal->append($path, 'preserve_started', 'database');
        $journal->append($path, 'preserve_completed', 'database');
        $journal->append($path, 'install_started', 'database');
        $journal->append($path, 'install_completed', 'database');

        try {
            (new RestoreRecovery($journal))->rollback($path, $resources);
            $this->fail('A failed directory sync must stop recovery.');
        } catch (RuntimeException) {
            $events = $journal->read($path)['events'];
            $this->assertSame('rollback_started', end($events)['event']);
            $this->assertSame('old', file_get_contents($resources['database']['previous']));
        }
    }

    private function installReplacement(): void
    {
        $journal = new RestoreJournal();
        $journal->append($this->journalPath, 'preserve_started', 'database');
        rename($this->resources['database']['target'], $this->resources['database']['previous']);
        $journal->append($this->journalPath, 'preserve_completed', 'database');
        $journal->append($this->journalPath, 'install_started', 'database');
        rename($this->resources['database']['replacement'], $this->resources['database']['target']);
        $journal->append($this->journalPath, 'install_completed', 'database');
    }
}
