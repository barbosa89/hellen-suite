<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;

use function is_resource;

class BackupArchivePublisher
{
    public function publish(string $source, string $destination): void
    {
        [$input, $before] = $this->openSource($source);

        $temporary = dirname($destination) . DIRECTORY_SEPARATOR . '.hellen-backup-' . bin2hex(random_bytes(16)) . '.tmp';
        $umask = umask(0077);

        try {
            $output = @fopen($temporary, 'x+b');
        } finally {
            umask($umask);
        }

        if ($output === false) {
            fclose($input);
            throw new RuntimeException('Unable to prepare the backup destination.');
        }

        $published = false;

        try {
            $this->copyAndVerify($input, $output, $source, $temporary, $before);

            if (! @link($temporary, $destination)) {
                throw new RuntimeException('Unable to publish the backup without overwriting an existing file.');
            }

            $published = true;
        } catch (Throwable $exception) {
            if (is_resource($input)) {
                fclose($input);
            }

            if (is_resource($output)) {
                fclose($output);
            }

            if (! @unlink($temporary)) {
                throw new RuntimeException('Backup publication failed and its temporary file could not be removed.', previous: $exception);
            }

            throw $exception;
        }

        if ($published && ! @unlink($temporary)) {
            throw new RuntimeException('The backup was published, but its temporary file could not be removed.');
        }
    }

    /** @return array{resource, string} */
    private function openSource(string $source): array
    {
        $before = @hash_file('sha256', $source);
        $input = @fopen($source, 'rb');

        if ($before === false || $input === false) {
            if (is_resource($input)) {
                fclose($input);
            }

            throw new RuntimeException('Unable to read the prepared backup.');
        }

        return [$input, $before];
    }

    /** @param resource $input @param resource $output */
    private function copyAndVerify($input, $output, string $source, string $temporary, string $before): void
    {
        $copied = stream_copy_to_stream($input, $output);
        $flushed = fflush($output);
        $synced = ! function_exists('fsync') || fsync($output);

        fclose($input);
        fclose($output);

        $after = @hash_file('sha256', $source);
        $actual = @hash_file('sha256', $temporary);

        if ($copied === false || ! $flushed || ! $synced || $after === false || $actual === false || ! hash_equals($before, $after) || ! hash_equals($before, $actual)) {
            throw new RuntimeException('Backup destination verification failed.');
        }
    }
}
