<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\AppKeyException;

use function is_string;
use function strlen;

final class NativeAppKey
{
    public static function load(string $directory): string
    {
        $keyPath = $directory . DIRECTORY_SEPARATOR . 'app.key';
        $lockPath = $directory . DIRECTORY_SEPARATOR . 'app.key.lock';
        $markerPath = $directory . DIRECTORY_SEPARATOR . 'app.key.initialized';

        $lock = @fopen($lockPath, 'c');

        if ($lock === false) {
            throw new AppKeyException('Unable to open the NativePHP encryption key lock.');
        }

        try {
            if (! flock($lock, LOCK_EX)) {
                throw new AppKeyException('Unable to lock the NativePHP encryption key.');
            }

            if (! file_exists($keyPath)) {
                self::create($keyPath, $markerPath);
            }

            $key = @file_get_contents($keyPath);
            $decoded = is_string($key) && str_starts_with($key, 'base64:')
                ? base64_decode(substr($key, 7), true)
                : false;

            if ($decoded === false || strlen($decoded) !== 32 || $key !== 'base64:' . base64_encode($decoded)) {
                throw new AppKeyException('The NativePHP encryption key is unreadable or invalid.');
            }

            if (! file_exists($markerPath) && @file_put_contents($markerPath, 'initialized', LOCK_EX) === false) {
                throw new AppKeyException('Unable to mark the NativePHP encryption key as initialized.');
            }

            return $key;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private static function create(string $keyPath, string $markerPath): void
    {
        if (file_exists($markerPath)) {
            throw new AppKeyException('The NativePHP encryption key is missing.');
        }

        $key = 'base64:' . base64_encode(random_bytes(32));
        $previousUmask = umask(0077);

        try {
            $file = @fopen($keyPath, 'x');
        } finally {
            umask($previousUmask);
        }

        if ($file === false) {
            throw new AppKeyException('Unable to create the NativePHP encryption key.');
        }

        try {
            if (fwrite($file, $key) !== strlen($key) || ! fflush($file)) {
                throw new AppKeyException('Unable to write the NativePHP encryption key.');
            }
        } finally {
            fclose($file);
        }
    }
}
