<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function is_array;

class GenerateVueTranslations extends Command
{
    /**
     * @var string
     */
    protected $signature = 'vue:translations
                            {--path= : Laravel language source path (defaults to Laravel language path)}';

    /**
     * @var string
     */
    protected $description = 'Generate a Vue-i18n JS translation file based on Laravel JSON / PHP translation files';

    protected string $languagePath;

    protected string $outputFile;

    public function handle(): int
    {
        $this->languagePath = $this->option('path')
            ? base_path($this->option('path'))
            : lang_path();

        if (!is_dir($this->languagePath)) {
            $this->error("\"{$this->languagePath}\" does not exists.");

            return self::FAILURE;
        }

        $this->outputFile = resource_path('js/lang/locales.js');

        $translations = $this->getTranslations([$this->languagePath]);

        $size = $this->generateVue18nFile($this->outputFile, $translations);

        $this->table(
            ['Language', 'Translations'],
            array_map(
                fn($language, $lines) => [$language, count($lines)],
                array_keys($translations),
                array_values($translations),
            ),
        );

        $this->line("<fg=yellow>{$this->outputFile}</fg=yellow> generated (<fg=green>{$size} bytes</fg=green>).");

        return self::SUCCESS;
    }

    public function getTranslations(array $paths): array
    {
        return Collection::make($paths)
            ->flatMap(fn($path) => $this->findTranslationFiles($path))
            ->groupBy(fn($paths) => $this->getTranslationLanguage($paths))
            ->map(fn(Collection $files) => $files->flatMap(fn($file) => $this->readTranslationFile($file)))
            ->map(fn($content) => $this->convertTranslations($content))
            ->all();
    }

    public function getTranslationLanguage(string $filename): string
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'json' => str_replace('.json', '', basename($filename)),
            'php' => basename(dirname($filename)),
        };
    }

    protected function findTranslationFiles(string $path): array|false
    {
        return glob($path . '/{,*/}*.{json,php}', GLOB_BRACE);
    }

    /**
     * @return array<string,string>
     */
    protected function readTranslationFile(string $filename): array
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'json' => json_decode(file_get_contents($filename), true),
            'php' => [basename($filename, '.php') => include ($filename)],
        };
    }

    /**
     * @return array<string,string|array>
     */
    protected function convertTranslations(Collection $lines): array
    {
        return $lines
            ->mapWithKeys(fn($translation, $key) => [
                $this->convertTranslation($key) => $this->convertTranslation($translation),
            ])
            ->all();
    }

    protected function convertTranslation(string|array $content): string|array
    {
        if (is_array($content)) {
            return array_combine(
                array_keys($content),
                array_map(fn($value) => $this->convertTranslation($value), $content),
            );
        }

        return Str::of($content)
            ->pipe($this->transformPluralization(...))
            ->pipe($this->transformCollonsToBraces(...))
            ->pipe($this->removeEscapeCharacter(...))
            ->value();
    }

    protected function removeEscapeCharacter(string $line): string
    {
        return preg_replace_callback(
            '/' . preg_quote('!', '/') . "(:\w+)/",
            fn($matches) => '{' . mb_substr($matches[0], 1) . '}',
            $line,
        );
    }

    /**
     * Turn Laravel style ":link" into vue-i18n style "{link}".
     */
    protected function transformCollonsToBraces(string $line): string
    {
        return preg_replace_callback(
            '/(?<!mailto|tel|' . preg_quote('!', '/') . "):\w+/",
            fn($matches) => '{' . mb_substr($matches[0], 1) . '}',
            $line,
        );
    }

    protected function transformPluralization(string $line): string
    {
        return preg_replace_callback(
            "/\{0\}\s(.*)\|\{1\}(.*)\|\[2,\*\](.*)/",
            fn($matches) => "{$matches[1]}|{$matches[2]}|{$matches[3]}",
            $line,
        );
    }

    /**
     * @param array<string,array> $translations
     */
    protected function generateVue18nFile(string $filename, array $translations): int|false
    {
        return file_put_contents(
            $filename,
            $this->convertTranslationsToVue18n($translations),
        );
    }

    /**
     * @param array<string,array> $translations
     */
    protected function convertTranslationsToVue18n(array $translations): string
    {
        $json = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return "export default {$json}" . PHP_EOL;
    }
}
