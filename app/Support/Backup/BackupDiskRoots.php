<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Illuminate\Contracts\Config\Repository;

use function is_array;
use function is_string;

class BackupDiskRoots
{
    public function __construct(private readonly Repository $config) {}

    /** @return array{hotels: list<string>, payments: list<string>} */
    public function resolve(): array
    {
        $default = $this->config->get('filesystems.default');

        if (! is_string($default) || $default === '') {
            throw new RuntimeException('The default attachment disk is unavailable.');
        }

        $hotelRoot = $this->localRoot($default);
        $publicRoot = $this->localRoot('public');

        return [
            'hotels' => array_values(array_unique([$hotelRoot, $publicRoot])),
            'payments' => [$publicRoot],
        ];
    }

    private function localRoot(string $disk): string
    {
        $settings = $this->config->get("filesystems.disks.{$disk}");

        if (! is_array($settings) || ($settings['driver'] ?? null) !== 'local' || ! is_string($settings['root'] ?? null) || $settings['root'] === '') {
            throw new RuntimeException('Backup requires configured local attachment disks.');
        }

        return $settings['root'];
    }
}
