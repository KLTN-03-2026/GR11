<?php

namespace App\Providers;

use App\Services\AI\Rag\LLM\GeminiClient;
use App\Services\AI\Rag\LLM\LlmClientInterface;
use App\Services\AI\Rag\LLM\OllamaClient;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeMissingComposerPsr4Prefixes();

        $this->app->bind(LlmClientInterface::class, function (): LlmClientInterface {
            $provider = strtolower((string) config('services.chat_llm.provider', 'gemini'));

            return match ($provider) {
                'ollama' => $this->app->make(OllamaClient::class),
                default => $this->app->make(GeminiClient::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->environment('local')) {
            return;
        }

        $fromEnvFile = $this->geminiApiKeyFromEnvFile();
        if ($fromEnvFile === null || $fromEnvFile === '') {
            return;
        }

        $loaded = (string) config('services.gemini.api_key', '');
        if ($loaded !== $fromEnvFile) {
            config(['services.gemini.api_key' => $fromEnvFile]);
        }
    }

    /**
     * On Windows, a stale GEMINI_API_KEY in OS/user environment overrides .env (Dotenv immutable).
     * In local dev, always prefer the value from .env when it differs.
     */
    private function geminiApiKeyFromEnvFile(): ?string
    {
        $path = base_path('.env');
        if (! is_readable($path)) {
            return null;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (! str_starts_with($line, 'GEMINI_API_KEY=')) {
                continue;
            }

            $value = trim(substr($line, strlen('GEMINI_API_KEY=')));

            return trim($value, " \t\n\r\0\x0B\"'");
        }

        return null;
    }

    private function mergeMissingComposerPsr4Prefixes(): void
    {
        $loader = null;
        foreach (spl_autoload_functions() as $autoload) {
            if (is_array($autoload) && ($autoload[0] ?? null) instanceof ClassLoader) {
                $loader = $autoload[0];
                break;
            }
        }

        if ($loader === null) {
            return;
        }

        $vendorDir = base_path('vendor');
        $baseDir = base_path();
        /** @var array<string, list<string>> $psr4 */
        $psr4 = require $vendorDir . '/composer/autoload_psr4.php';

        $required = [
            'Barryvdh\\DomPDF\\',
            'Dompdf\\',
            'FontLib\\',
            'Svg\\',
            'Masterminds\\',
        ];

        foreach ($required as $namespace) {
            if (! isset($psr4[$namespace][0])) {
                continue;
            }
            $loader->addPsr4($namespace, rtrim($psr4[$namespace][0], '/\\') . DIRECTORY_SEPARATOR);
        }

        $classmapFile = $vendorDir . '/composer/autoload_classmap.php';
        if (! is_readable($classmapFile)) {
            return;
        }

        /** @var array<string, string> $classmap */
        $classmap = require $classmapFile;
        $dompdfMap = [];
        foreach ($classmap as $class => $path) {
            if (str_starts_with($class, 'Dompdf\\') && is_file($path)) {
                $dompdfMap[$class] = $path;
            }
        }
        if ($dompdfMap !== []) {
            $loader->addClassMap($dompdfMap);
        }
    }
}
