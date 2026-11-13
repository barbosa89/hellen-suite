<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\RestoreTargetResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreTargetResolverTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-targets-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/default');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_resolves_targets_even_when_the_database_was_moved_during_an_interrupted_restore(): void
    {
        $targets = (new RestoreTargetResolver())->resolve($this->directory . '/live.sqlite', [
            'hotels' => [$this->directory . '/default', $this->directory . '/public'],
            'payments' => [$this->directory . '/public'],
        ]);

        $this->assertSame([
            'database' => $this->directory . '/live.sqlite',
            'hotels-public' => $this->directory . '/public/hotels',
            'hotels-default' => $this->directory . '/default/hotels',
            'payments-public' => $this->directory . '/public/payments',
        ], $targets);
    }

    #[Test]
    public function it_rejects_a_symbolic_link_at_a_destination(): void
    {
        if (! @symlink($this->directory . '/missing', $this->directory . '/live.sqlite')) {
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        $this->expectException(RuntimeException::class);
        (new RestoreTargetResolver())->resolve($this->directory . '/live.sqlite', [
            'hotels' => [$this->directory . '/public'],
            'payments' => [$this->directory . '/public'],
        ]);
    }
}
