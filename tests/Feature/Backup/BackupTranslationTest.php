<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BackupTranslationTest extends TestCase
{
    #[Test]
    public function both_languages_expose_the_same_backup_keys(): void
    {
        $english = require lang_path('en/backup.php');
        $spanish = require lang_path('es/backup.php');

        $this->assertSame($this->flatten($english), $this->flatten($spanish));
    }

    #[Test]
    public function the_backup_copy_names_the_sensitive_contents(): void
    {
        foreach (['en', 'es'] as $locale) {
            $translations = require lang_path($locale . '/backup.php');
            $warnings = $translations['warnings'];

            $this->assertArrayHasKey('plain', $warnings);
            $this->assertArrayHasKey('storage', $warnings);
            $this->assertArrayHasKey('keys', $warnings);
            $this->assertNotSame('', $translations['messages']['export_completed']);
            $this->assertNotSame('', $translations['messages']['restore_failed']);
        }
    }

    private function flatten(array $translations, string $prefix = ''): array
    {
        $flat = [];

        foreach ($translations as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $flat += $this->flatten($value, $path);

                continue;
            }

            $flat[$path] = true;
        }

        ksort($flat);

        return array_keys($flat);
    }
}
