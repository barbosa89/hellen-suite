<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use PDO;
use PDOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function is_array;
use function is_int;
use function is_string;

/** Verify while all writers and database connections remain closed. */
class RestoreInstalledVerifier
{
    public function verify(array $resources, array $files, array $manifest): bool
    {
        if (! is_array($manifest['files'] ?? null) || ! isset($resources['database']['target'], $manifest['files']['database.sqlite'])) {
            throw new RuntimeException('The installed restore plan is invalid.');
        }

        $treeResources = $resources;
        unset($treeResources['database']);

        if (array_diff_key($treeResources, $files) !== [] || array_diff_key($files, $treeResources) !== []) {
            throw new RuntimeException('The installed restore plan is incomplete.');
        }

        $database = $resources['database']['target'];
        $this->verifyFile($database, $manifest['files']['database.sqlite']);
        $this->verifyDatabase($database);
        $expectedFiles = ['database.sqlite' => true];

        foreach ($files as $resource => $names) {
            if (! is_array($names) || ! isset($resources[$resource]['target'])) {
                throw new RuntimeException('The installed restore plan is invalid.');
            }

            $this->verifyTree($resources[$resource]['target'], $names, $manifest['files'], $expectedFiles);
        }

        if (array_diff_key($manifest['files'], $expectedFiles) !== [] || array_diff_key($expectedFiles, $manifest['files']) !== []) {
            throw new RuntimeException('The installed restore plan is incomplete.');
        }

        return true;
    }

    private function verifyDatabase(string $path): void
    {
        foreach (['-wal', '-shm', '-journal'] as $suffix) {
            if (file_exists($path . $suffix) || is_link($path . $suffix)) {
                throw new RuntimeException('Unexpected SQLite journal files prevent restore verification.');
            }
        }

        try {
            $database = new PDO('sqlite:' . $path, options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $database->exec('PRAGMA query_only = ON');
            $result = $database->query('PRAGMA integrity_check')->fetchAll(PDO::FETCH_COLUMN);
            $database = null;
        } catch (PDOException $exception) {
            throw new RuntimeException('The restored database failed verification.', previous: $exception);
        }

        if ($result !== ['ok']) {
            throw new RuntimeException('The restored database failed verification.');
        }
    }

    private function verifyTree(string $root, array $names, array $metadata, array &$expectedFiles): void
    {
        if (! is_dir($root) || is_link($root)) {
            throw new RuntimeException('A restored attachment tree is invalid.');
        }

        $expected = [];

        foreach ($names as $name) {
            if (! is_string($name) || ! isset($metadata[$name]) || isset($expectedFiles[$name])) {
                throw new RuntimeException('The installed restore plan is invalid.');
            }

            $parts = explode('/', $name);
            array_shift($parts);
            $relative = implode('/', $parts);

            if ($relative === '' || ! (new BackupPathPolicy())->validComponents($parts)) {
                throw new RuntimeException('The installed restore plan is invalid.');
            }

            $expected[$relative] = $name;
            $expectedFiles[$name] = true;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $entry) {
            $this->verifyTreeEntry($entry, $iterator->getSubPathName(), $expected, $metadata);
        }

        if ($expected !== []) {
            throw new RuntimeException('A restored attachment is missing.');
        }
    }

    private function verifyTreeEntry(SplFileInfo $entry, string $relative, array &$expected, array $metadata): void
    {
        if ($entry->isLink()) {
            throw new RuntimeException('A restored attachment is invalid.');
        }

        if ($entry->isDir()) {
            return;
        }

        $relative = str_replace('\\', '/', $relative);

        if (! $entry->isFile() || ! isset($expected[$relative])) {
            throw new RuntimeException('A restored attachment is unexpected.');
        }

        $this->verifyFile($entry->getPathname(), $metadata[$expected[$relative]]);
        unset($expected[$relative]);
    }

    private function verifyFile(string $path, array $metadata): void
    {
        if (! is_file($path) || is_link($path) || ! is_int($metadata['size'] ?? null) || ! is_string($metadata['sha256'] ?? null)) {
            throw new RuntimeException('A restored file is invalid.');
        }

        clearstatcache(true, $path);
        $size = filesize($path);
        $hash = hash_file('sha256', $path);

        if ($size !== $metadata['size'] || $hash === false || ! hash_equals($metadata['sha256'], $hash)) {
            throw new RuntimeException('A restored file failed verification.');
        }
    }
}
