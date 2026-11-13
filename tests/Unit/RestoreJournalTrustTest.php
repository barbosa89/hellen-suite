<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\RestoreJournalTrust;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreJournalTrustTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-journal-trust-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_reconstructs_sibling_paths_from_trusted_destinations_and_transaction_id(): void
    {
        $journal = $this->journal();

        $resources = (new RestoreJournalTrust())->validate($journal, ['database' => $this->directory . '/live.sqlite']);

        $this->assertSame($journal['resources'], $resources);
    }

    #[Test]
    public function it_rejects_a_journal_with_a_replacement_path_outside_the_expected_name(): void
    {
        $journal = $this->journal();
        $journal['resources']['database']['replacement'] = $this->directory . '/unrelated.sqlite';

        $this->expectException(RuntimeException::class);
        (new RestoreJournalTrust())->validate($journal, ['database' => $this->directory . '/live.sqlite']);
    }

    #[Test]
    public function it_rejects_journals_with_extra_resources(): void
    {
        $journal = $this->journal();
        $journal['resources']['extra'] = $journal['resources']['database'];

        $this->expectException(RuntimeException::class);
        (new RestoreJournalTrust())->validate($journal, ['database' => $this->directory . '/live.sqlite']);
    }

    #[Test]
    public function it_rejects_a_symlink_at_a_recorded_resource_path(): void
    {
        $journal = $this->journal();

        if (! @symlink($this->directory . '/missing', $journal['resources']['database']['replacement'])) {
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        $this->expectException(RuntimeException::class);
        (new RestoreJournalTrust())->validate($journal, ['database' => $this->directory . '/live.sqlite']);
    }

    private function journal(): array
    {
        return [
            'transaction' => 'abc123',
            'resources' => [
                'database' => [
                    'target' => $this->directory . '/live.sqlite',
                    'previous' => $this->directory . '/.live.sqlite.restore-abc123-previous',
                    'replacement' => $this->directory . '/.live.sqlite.restore-abc123-replacement',
                    'existed' => true,
                ],
            ],
        ];
    }
}
