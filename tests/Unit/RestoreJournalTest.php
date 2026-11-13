<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\RestoreJournal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreJournalTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-journal-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_persists_the_resource_plan_and_sequenced_events(): void
    {
        $path = $this->directory . '/restore.journal';
        $resources = ['database' => ['target' => '/data/live.sqlite', 'previous' => '/data/old.sqlite', 'replacement' => '/data/new.sqlite']];
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-1', $resources);
        $journal->append($path, 'preserve_started', 'database');
        $journal->append($path, 'preserve_completed', 'database');

        $result = $journal->read($path);
        $this->assertSame('transaction-1', $result['transaction']);
        $this->assertSame($resources, $result['resources']);
        $this->assertSame([
            ['sequence' => 1, 'event' => 'preserve_started', 'resource' => 'database'],
            ['sequence' => 2, 'event' => 'preserve_completed', 'resource' => 'database'],
        ], $result['events']);
        $this->assertFalse($result['incomplete_tail']);
    }

    #[Test]
    public function it_reports_and_discards_only_an_incomplete_final_record_before_appending(): void
    {
        $path = $this->directory . '/restore.journal';
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-1', ['database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new']]);
        $journal->append($path, 'preserve_started', 'database');
        file_put_contents($path, '{"incomplete":', FILE_APPEND);

        $this->assertTrue($journal->read($path)['incomplete_tail']);
        $journal->append($path, 'preserve_completed', 'database');
        $this->assertFalse($journal->read($path)['incomplete_tail']);
        $this->assertCount(2, $journal->read($path)['events']);
    }

    #[Test]
    public function it_rejects_corrupted_complete_records(): void
    {
        $path = $this->directory . '/restore.journal';
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-1', ['database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new']]);
        file_put_contents($path, "invalid\n", FILE_APPEND);

        $this->expectException(RuntimeException::class);
        $journal->read($path);
    }

    #[Test]
    public function it_never_overwrites_an_existing_journal(): void
    {
        $path = $this->directory . '/restore.journal';
        file_put_contents($path, 'existing');

        try {
            (new RestoreJournal())->create($path, 'transaction-1', ['database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new']]);
            $this->fail('An existing recovery journal must be preserved.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($path));
        }
    }

    #[Test]
    public function it_fails_closed_if_the_journal_directory_cannot_be_synchronized(): void
    {
        $link = $this->directory . '/linked-directory';

        if (! @symlink($this->directory, $link)) {
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        try {
            (new RestoreJournal())->create($link . '/restore.journal', 'transaction-1', [
                'database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new'],
            ]);
            $this->fail('The journal directory must be synchronized before proceeding.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/restore.journal');
        }
    }

    #[Test]
    public function it_rejects_out_of_order_events_without_changing_the_journal(): void
    {
        $path = $this->directory . '/restore.journal';
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-1', ['database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new']]);
        $before = file_get_contents($path);

        try {
            $journal->append($path, 'install_started', 'database');
            $this->fail('An installation cannot start before preserving the previous resource.');
        } catch (RuntimeException) {
            $this->assertSame($before, file_get_contents($path));
        }
    }

    #[Test]
    public function it_tracks_each_resource_independently_and_allows_rollback(): void
    {
        $path = $this->directory . '/restore.journal';
        $resources = [
            'database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new'],
            'hotels' => ['target' => '/hotels', 'previous' => '/hotels-old', 'replacement' => '/hotels-new'],
        ];
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-1', $resources);
        $journal->append($path, 'preserve_started', 'database');
        $journal->append($path, 'preserve_started', 'hotels');
        $journal->append($path, 'preserve_completed', 'database');
        $journal->append($path, 'install_started', 'database');
        $journal->append($path, 'rollback_started', 'database');
        $journal->append($path, 'rollback_completed', 'database');

        $this->assertCount(6, $journal->read($path)['events']);
    }

    #[Test]
    public function it_rejects_forward_operations_after_rollback_begins(): void
    {
        $path = $this->directory . '/restore.journal';
        $journal = new RestoreJournal();
        $journal->create($path, 'transaction-1', [
            'database' => ['target' => '/live', 'previous' => '/old', 'replacement' => '/new'],
            'hotels' => ['target' => '/hotels', 'previous' => '/hotels-old', 'replacement' => '/hotels-new'],
        ]);
        $journal->append($path, 'preserve_started', 'database');
        $journal->append($path, 'rollback_started', 'database');

        $this->expectException(RuntimeException::class);
        $journal->append($path, 'preserve_started', 'hotels');
    }

    #[Test]
    public function it_rejects_resource_paths_with_traversal(): void
    {
        $path = $this->directory . '/restore.journal';

        try {
            (new RestoreJournal())->create($path, 'transaction-1', [
                'database' => ['target' => '/data/../live.sqlite', 'previous' => '/data/old.sqlite', 'replacement' => '/data/new.sqlite'],
            ]);
            $this->fail('Journal paths must not contain traversal.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($path);
        }
    }

    #[Test]
    public function it_rejects_a_replacement_on_a_different_directory(): void
    {
        $path = $this->directory . '/restore.journal';

        $this->expectException(RuntimeException::class);
        (new RestoreJournal())->create($path, 'transaction-1', [
            'database' => ['target' => '/data/live.sqlite', 'previous' => '/data/old.sqlite', 'replacement' => '/other/new.sqlite'],
        ]);
    }

    #[Test]
    public function it_rejects_overlapping_resource_paths(): void
    {
        $path = $this->directory . '/restore.journal';

        $this->expectException(RuntimeException::class);
        (new RestoreJournal())->create($path, 'transaction-1', [
            'database' => ['target' => '/data/hotels/live.sqlite', 'previous' => '/data/hotels/old.sqlite', 'replacement' => '/data/hotels/new.sqlite'],
            'hotels' => ['target' => '/data/hotels', 'previous' => '/data/hotels-old', 'replacement' => '/data/hotels-new'],
        ]);
    }

    #[Test]
    public function it_rejects_windows_paths_that_differ_only_by_case(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $this->markTestSkipped('Windows path semantics require a Windows runtime.');
        }

        $path = $this->directory . '/restore.journal';

        $this->expectException(RuntimeException::class);
        (new RestoreJournal())->create($path, 'transaction-1', [
            'database' => ['target' => 'C:\\Data\\live.sqlite', 'previous' => 'C:\\Data\\LIVE.sqlite', 'replacement' => 'C:\\Data\\new.sqlite'],
        ]);
    }

    #[Test]
    public function it_rejects_windows_alternate_data_stream_paths(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $this->markTestSkipped('Windows path semantics require a Windows runtime.');
        }

        $path = $this->directory . '/restore.journal';

        $this->expectException(RuntimeException::class);
        (new RestoreJournal())->create($path, 'transaction-1', [
            'database' => ['target' => 'C:\\Data\\live.sqlite:stream', 'previous' => 'C:\\Data\\old.sqlite', 'replacement' => 'C:\\Data\\new.sqlite'],
        ]);
    }
}
