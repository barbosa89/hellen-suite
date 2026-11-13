<?php

declare(strict_types=1);

namespace App\Support\Backup;

use Native\Desktop\Dialog;

use function is_string;

/** Native file dialogs, isolated so the workflow can be exercised without the desktop runtime. */
class DesktopFileDialog
{
    public function save(string $title, string $button, string $defaultPath): null|string
    {
        return $this->normalize(
            Dialog::new()
                ->title($title)
                ->button($button)
                ->defaultPath($defaultPath)
                ->filter('Backup', ['zip'])
                ->save()
        );
    }

    public function open(string $title, string $button, string $defaultPath): null|string
    {
        return $this->normalize(
            Dialog::new()
                ->title($title)
                ->button($button)
                ->defaultPath($defaultPath)
                ->filter('Backup', ['zip'])
                ->files()
                ->open()
        );
    }

    private function normalize(mixed $result): null|string
    {
        if (! is_string($result) || $result === '') {
            return null;
        }

        return $result;
    }
}
