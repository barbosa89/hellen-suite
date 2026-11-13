<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\BackupPathPolicy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BackupPathPolicyTest extends TestCase
{
    #[Test]
    public function it_validates_archive_path_components_without_normalizing_them(): void
    {
        $policy = new BackupPathPolicy();

        $this->assertTrue($policy->validComponents(['hotels', 'photos', 'image.jpg']));

        foreach (['', '.', '..', 'a\\b', 'a:b', "a\0b"] as $component) {
            $this->assertFalse($policy->validComponents(['hotels', $component]));
        }
    }
}
