<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BackupClassExtensibilityTest extends TestCase
{
    #[Test]
    public function backup_classes_are_not_final(): void
    {
        $files = glob(__DIR__ . '/../../app/Support/Backup/*.php');
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $class = 'App\\Support\\Backup\\' . basename($file, '.php');
            $this->assertFalse((new ReflectionClass($class))->isFinal(), $class . ' must not be final.');
        }
    }
}
