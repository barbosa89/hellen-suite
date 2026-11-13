<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use JsonException;
use Symfony\Component\Filesystem\Path;

use function array_key_exists;
use function array_slice;
use function count;
use function in_array;
use function is_array;
use function is_string;
use function strlen;

/** Append-only recovery records. Callers must separately validate all recorded paths. */
class RestoreJournal
{
    private const MAX_BYTES = 1048576;

    private const EVENTS = [
        'preserve_started', 'preserve_completed',
        'install_started', 'install_completed',
        'verify_completed', 'rollback_started', 'rollback_completed',
    ];

    public function create(string $path, string $transaction, array $resources): void
    {
        $header = ['format' => 1, 'transaction' => $transaction, 'resources' => $resources];
        $this->validateHeader($header);
        (new RestoreDirectorySync())->sync(dirname($path));
        $umask = umask(0077);

        try {
            $file = @fopen($path, 'x+b');
        } finally {
            umask($umask);
        }

        if ($file === false) {
            throw new RuntimeException('Unable to create the restore journal.');
        }

        try {
            if (! flock($file, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Unable to lock the restore journal.');
            }

            $this->writeRecord($file, $header);
        } finally {
            fclose($file);
        }

        (new RestoreDirectorySync())->sync(dirname($path));
    }

    public function append(string $path, string $event, string $resource): void
    {
        $file = @fopen($path, 'r+b');

        if ($file === false) {
            throw new RuntimeException('Unable to open the restore journal.');
        }

        try {
            if (! flock($file, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Unable to lock the restore journal.');
            }

            [$journal, $validBytes] = $this->parse($file);

            if (! in_array($event, self::EVENTS, true) || ! array_key_exists($resource, $journal['resources'])) {
                throw new RuntimeException('Invalid restore journal event.');
            }

            $next = ['sequence' => count($journal['events']) + 1, 'event' => $event, 'resource' => $resource];
            $this->validateTransitions([...$journal['events'], $next]);

            if (! ftruncate($file, $validBytes) || fseek($file, $validBytes) !== 0) {
                throw new RuntimeException('Unable to prepare the restore journal.');
            }

            $this->writeRecord($file, $next);
        } finally {
            fclose($file);
        }
    }

    public function read(string $path): array
    {
        $file = @fopen($path, 'rb');

        if ($file === false) {
            throw new RuntimeException('Unable to open the restore journal.');
        }

        try {
            if (! flock($file, LOCK_SH | LOCK_NB)) {
                throw new RuntimeException('Unable to lock the restore journal.');
            }

            return $this->parse($file)[0];
        } finally {
            fclose($file);
        }
    }

    /** @param resource $file */
    private function writeRecord($file, array $record): void
    {
        $json = json_encode($record, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $line = $json . "\t" . hash('sha256', $json) . "\n";
        $position = ftell($file);

        if ($position === false || strlen($line) > self::MAX_BYTES - $position) {
            throw new RuntimeException('Restore journal size limit exceeded.');
        }

        $written = 0;

        while ($written < strlen($line)) {
            $count = fwrite($file, substr($line, $written));

            if ($count === false || $count === 0) {
                throw new RuntimeException('Unable to write the restore journal.');
            }

            $written += $count;
        }

        if (! fflush($file) || ! function_exists('fsync') || ! fsync($file)) {
            throw new RuntimeException('Unable to synchronize the restore journal.');
        }
    }

    /** @param resource $file */
    private function parse($file): array
    {
        rewind($file);
        $content = stream_get_contents($file, self::MAX_BYTES + 1);

        if ($content === false || strlen($content) > self::MAX_BYTES) {
            throw new RuntimeException('Invalid restore journal.');
        }

        $lastNewline = strrpos($content, "\n");

        if ($lastNewline === false) {
            throw new RuntimeException('Invalid restore journal.');
        }

        $validBytes = $lastNewline + 1;
        $lines = explode("\n", substr($content, 0, $lastNewline));
        $records = array_map($this->decodeRecord(...), $lines);
        $header = array_shift($records);
        $this->validateHeader($header);
        $this->validateEvents($records, $header['resources']);
        $this->validateTransitions($records);

        return [[
            'transaction' => $header['transaction'],
            'resources' => $header['resources'],
            'events' => $records,
            'incomplete_tail' => $validBytes !== strlen($content),
        ], $validBytes];
    }

    private function decodeRecord(string $line): array
    {
        $separator = strrpos($line, "\t");

        if ($separator === false) {
            throw new RuntimeException('Invalid restore journal.');
        }

        $json = substr($line, 0, $separator);
        $checksum = substr($line, $separator + 1);

        if (! hash_equals(hash('sha256', $json), $checksum)) {
            throw new RuntimeException('Invalid restore journal.');
        }

        try {
            $record = json_decode($json, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Invalid restore journal.', previous: $exception);
        }

        if (! is_array($record)) {
            throw new RuntimeException('Invalid restore journal.');
        }

        return $record;
    }

    private function validateEvents(array $records, array $resources): void
    {
        foreach ($records as $index => $record) {
            if (($record['sequence'] ?? null) !== $index + 1 || ! in_array($record['event'] ?? null, self::EVENTS, true) || ! is_string($record['resource'] ?? null) || ! array_key_exists($record['resource'], $resources)) {
                throw new RuntimeException('Invalid restore journal.');
            }
        }
    }

    private function validateTransitions(array $events): void
    {
        $states = [];
        $rollingBack = false;

        foreach ($events as $record) {
            $event = $record['event'];
            $resource = $record['resource'];
            $previous = $states[$resource] ?? null;
            $isRollback = in_array($event, ['rollback_started', 'rollback_completed'], true);

            if (! $this->transitionAllowed($previous, $event) || ($rollingBack && ! $isRollback)) {
                throw new RuntimeException('Invalid restore journal event order.');
            }

            $rollingBack = $rollingBack || $isRollback;
            $states[$resource] = $event;
        }
    }

    private function transitionAllowed(null|string $previous, string $event): bool
    {
        return match ($event) {
            'preserve_started' => $previous === null,
            'preserve_completed' => $previous === 'preserve_started',
            'install_started' => $previous === 'preserve_completed',
            'install_completed' => $previous === 'install_started',
            'verify_completed' => $previous === 'install_completed',
            'rollback_started' => in_array($previous, ['preserve_started', 'preserve_completed', 'install_started', 'install_completed', 'verify_completed'], true),
            'rollback_completed' => $previous === 'rollback_started',
            default => false,
        };
    }

    private function validateHeader(mixed $header): void
    {
        if (! is_array($header) || ($header['format'] ?? null) !== 1 || ! is_string($header['transaction'] ?? null) || ! preg_match('/\A[a-zA-Z0-9_-]{1,100}\z/', $header['transaction']) || ! is_array($header['resources'] ?? null) || $header['resources'] === []) {
            throw new RuntimeException('Invalid restore journal.');
        }

        $allPaths = [];

        foreach ($header['resources'] as $name => $paths) {
            if (! is_string($name) || ! preg_match('/\A[a-zA-Z0-9_-]{1,100}\z/', $name) || ! is_array($paths)) {
                throw new RuntimeException('Invalid restore journal.');
            }

            $normalized = [];

            foreach (['target', 'previous', 'replacement'] as $key) {
                $value = $this->normalizeResourcePath($paths[$key] ?? null);
                $normalized[$key] = $value;
                $allPaths[] = $value;
            }

            if (Path::getDirectory($normalized['target']) !== Path::getDirectory($normalized['previous']) || Path::getDirectory($normalized['target']) !== Path::getDirectory($normalized['replacement'])) {
                throw new RuntimeException('Restore resources must be staged alongside their destinations.');
            }
        }

        $this->rejectOverlappingPaths($allPaths);
    }

    private function normalizeResourcePath(mixed $path): string
    {
        if (! is_string($path) || $path === '' || str_contains($path, "\0")) {
            throw new RuntimeException('Invalid restore journal.');
        }

        $value = str_replace('\\', '/', $path);
        $parts = explode('/', $value);

        if (! Path::isAbsolute($value) || in_array('.', $parts, true) || in_array('..', $parts, true)) {
            throw new RuntimeException('Invalid restore journal path.');
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $this->validateWindowsPath($value);
        }

        $value = Path::canonicalize($value);

        if ($value === '/' || preg_match('/\A[a-zA-Z]:\/$/', $value)) {
            throw new RuntimeException('Invalid restore journal path.');
        }

        if (PHP_OS_FAMILY === 'Windows' || PHP_OS_FAMILY === 'Darwin' || preg_match('/\A[a-zA-Z]:\//', $value) || str_starts_with($value, '//')) {
            $value = strtolower($value);
        }

        return $value;
    }

    private function validateWindowsPath(string $value): void
    {
        $withoutDrive = preg_replace('/\A[a-zA-Z]:/', '', $value);

        if ($withoutDrive === null || str_contains($withoutDrive, ':')) {
            throw new RuntimeException('Invalid restore journal path.');
        }

        foreach (explode('/', $withoutDrive) as $part) {
            if ($part !== '' && (str_ends_with($part, '.') || str_ends_with($part, ' ') || preg_match('/\A(?:con|prn|aux|nul|com[1-9]|lpt[1-9])(?:\.|\z)/i', $part))) {
                throw new RuntimeException('Invalid restore journal path.');
            }
        }
    }

    private function rejectOverlappingPaths(array $allPaths): void
    {
        foreach ($allPaths as $index => $path) {
            foreach (array_slice($allPaths, $index + 1) as $other) {
                if ($path === $other || str_starts_with($path, $other . '/') || str_starts_with($other, $path . '/')) {
                    throw new RuntimeException('Restore resource paths must not overlap.');
                }
            }
        }
    }
}
