<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_resource;
use function is_string;

/** Internal staging only; the caller must hold desktop-wide exclusive access. */
class RestoreReplacementStager
{
    public function __construct(private readonly Filesystem $filesystem) {}

    public function stage(string $dataset, array $plan, array $manifest, BackupMaintenanceLock $lock): void
    {
        if (! $lock->isExclusive() || ! is_dir($dataset) || is_link($dataset)) {
            throw new RuntimeException('Exclusive access and a private restore dataset are required.');
        }

        $this->validatePlan($plan, $manifest);
        $created = [];

        try {
            $this->stageDatabase($dataset, $plan['resources']['database'], $manifest['files']['database.sqlite'], $created);

            foreach ($plan['files'] as $resource => $names) {
                $target = $plan['resources'][$resource]['replacement'];

                if (! @mkdir($target, 0700)) {
                    throw new RuntimeException('Unable to prepare a restore attachment tree.');
                }

                $created[] = $target;
                $this->stageTree($dataset, $target, $names, $manifest['files']);
            }
        } catch (Throwable $exception) {
            try {
                $this->filesystem->remove($created);
            } catch (Throwable $cleanupException) {
                throw new RestoreFailure('Restore staging failed and temporary replacements could not be removed.', $exception, $cleanupException);
            }

            throw $exception;
        }
    }

    private function validatePlan(array $plan, array $manifest): void
    {
        if (! is_array($plan['resources'] ?? null) || ! is_array($plan['files'] ?? null) || ! is_array($manifest['files'] ?? null) || ! is_array($manifest['attachment_disks'] ?? null) || ! isset($plan['resources']['database'], $manifest['files']['database.sqlite'])) {
            throw new RuntimeException('Invalid restore staging plan.');
        }

        if (! isset($plan['resources']['hotels-public'], $plan['resources']['payments-public'], $plan['files']['hotels-public'], $plan['files']['payments-public'])) {
            throw new RuntimeException('The restore staging plan is incomplete.');
        }

        $names = [];

        foreach ($plan['resources'] as $resource => $paths) {
            $this->validateResource($resource, $paths, $plan['files']);
        }

        foreach ($plan['files'] as $resource => $files) {
            if (! isset($plan['resources'][$resource]) || ! is_array($files)) {
                throw new RuntimeException('Invalid restore staging plan.');
            }

            foreach ($files as $name) {
                $this->validateName($resource, $name);
                $this->validateDiskRole($resource, $name, $plan['resources'], $manifest['attachment_disks']);

                if (isset($names[$name]) || ! isset($manifest['files'][$name])) {
                    throw new RuntimeException('Invalid restore staging plan.');
                }

                $names[$name] = true;
            }
        }

        $files = $manifest['files'];
        unset($files['database.sqlite']);

        if (array_diff_key($files, $names) !== [] || array_diff_key($names, $files) !== []) {
            throw new RuntimeException('The restore staging plan is incomplete.');
        }
    }

    private function validateDiskRole(string $resource, string $name, array $resources, array $mapping): void
    {
        $disk = $mapping[$name] ?? null;

        if (! in_array($disk, ['public', 'default'], true)) {
            throw new RuntimeException('Invalid backup attachment disk.');
        }

        if (str_starts_with($name, 'payments/')) {
            $expected = 'payments-public';

            if ($disk !== 'public') {
                throw new RuntimeException('Invalid backup attachment disk.');
            }
        } else {
            $expected = $disk === 'default' && isset($resources['hotels-default']) ? 'hotels-default' : 'hotels-public';
        }

        if ($resource !== $expected) {
            throw new RuntimeException('The restore attachment disk does not match its plan.');
        }
    }

    private function validateResource(mixed $resource, mixed $paths, array $files): void
    {
        if (! is_string($resource) || ! in_array($resource, ['database', 'hotels-public', 'hotels-default', 'payments-public'], true) || ! is_array($paths)) {
            throw new RuntimeException('Invalid restore staging plan.');
        }

        foreach (['target', 'previous', 'replacement'] as $key) {
            if (! is_string($paths[$key] ?? null) || $paths[$key] === '') {
                throw new RuntimeException('Invalid restore staging plan.');
            }
        }

        if (($resource !== 'database' && ! isset($files[$resource])) || ! is_bool($paths['existed'] ?? null)) {
            throw new RuntimeException('Invalid restore staging plan.');
        }

        if (is_link($paths['target']) || file_exists($paths['target']) !== $paths['existed'] || file_exists($paths['previous']) || is_link($paths['previous']) || file_exists($paths['replacement']) || is_link($paths['replacement'])) {
            throw new RuntimeException('A restore destination changed before staging.');
        }
    }

    private function validateName(string $resource, mixed $name): void
    {
        $prefix = str_starts_with($resource, 'hotels-') ? 'hotels/' : 'payments/';

        if (! is_string($name) || ! str_starts_with($name, $prefix)) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }

        if (! (new BackupPathPolicy())->validComponents(explode('/', $name))) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }
    }

    private function stageDatabase(string $dataset, array $resource, array $metadata, array &$created): void
    {
        $this->copyVerified($dataset . DIRECTORY_SEPARATOR . 'database.sqlite', $resource['replacement'], $metadata, $created);
    }

    private function stageTree(string $dataset, string $target, array $names, array $files): void
    {
        foreach ($names as $name) {
            $parts = explode('/', $name);
            array_shift($parts);
            $filename = array_pop($parts);
            $parent = $target;

            foreach ($parts as $part) {
                $parent .= DIRECTORY_SEPARATOR . $part;

                if (is_link($parent) || (! is_dir($parent) && ! @mkdir($parent, 0700))) {
                    throw new RuntimeException('Unable to prepare a restore attachment tree.');
                }
            }

            $source = $this->sourcePath($dataset, $name);
            $ignored = [];
            $this->copyVerified($source, $parent . DIRECTORY_SEPARATOR . $filename, $files[$name], $ignored);
        }
    }

    private function sourcePath(string $dataset, string $name): string
    {
        $path = rtrim($dataset, '/\\');

        foreach (explode('/', $name) as $part) {
            $path .= DIRECTORY_SEPARATOR . $part;

            if (is_link($path)) {
                throw new RuntimeException('Invalid restore source.');
            }
        }

        return $path;
    }

    private function copyVerified(string $source, string $target, array $metadata, array &$created): void
    {
        if (! is_int($metadata['size'] ?? null) || $metadata['size'] < 0 || $metadata['size'] === PHP_INT_MAX || ! is_string($metadata['sha256'] ?? null) || ! preg_match('/\A[a-f0-9]{64}\z/', $metadata['sha256']) || is_link($source) || ! is_file($source)) {
            throw new RuntimeException('Invalid restore source.');
        }

        $input = @fopen($source, 'rb');

        if ($input === false) {
            throw new RuntimeException('Unable to read a restore source.');
        }

        $umask = umask(0077);

        try {
            $output = @fopen($target, 'x+b');
        } finally {
            umask($umask);
        }

        if ($output === false) {
            fclose($input);
            throw new RuntimeException('Unable to create a restore replacement.');
        }

        $created[] = $target;

        try {
            $size = stream_copy_to_stream($input, $output, $metadata['size'] + 1);
            $flushed = fflush($output);
            $synced = function_exists('fsync') && fsync($output);
        } finally {
            if (is_resource($input)) {
                fclose($input);
            }

            if (is_resource($output)) {
                fclose($output);
            }
        }

        $hash = @hash_file('sha256', $target);

        if ($size !== $metadata['size'] || ! $flushed || ! $synced || $hash === false || ! hash_equals($metadata['sha256'], $hash)) {
            throw new RuntimeException('Restore replacement verification failed.');
        }
    }
}
