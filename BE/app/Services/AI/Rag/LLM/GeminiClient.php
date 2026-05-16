<?php

namespace App\Services\AI\Rag\LLM;

use App\Services\AI\Rag\Support\LlmResponseSanitizer;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiClient implements LlmClientInterface
{
    public function __construct(
        private readonly LlmResponseSanitizer $sanitizer = new LlmResponseSanitizer(),
    ) {}

    /**
     * @throws RuntimeException
     */
    public function generateText(string $prompt): string
    {
        return $this->requestGemini($prompt, []);
    }

    /**
     * @param list<array{role:string, content:string}> $history
     * @throws RuntimeException
     */
    public function generateFromConversation(string $systemPrompt, array $history, string $userMessage): string
    {
        return $this->requestGemini($systemPrompt, array_merge($history, [
            ['role' => 'user', 'content' => $userMessage],
        ]));
    }

    /**
     * Gemini native function calling.
     *
     * @param list<array<string, mixed>> $contents Gemini-format conversation turns
     * @param list<array<string, mixed>> $functionDeclarations Gemini functionDeclarations entries
     * @return array{text:?string, functionCall:?array{name:string, args:array<string, mixed>}}
     * @throws RuntimeException
     */
    public function generateWithTools(
        string $systemPrompt,
        array $contents,
        array $functionDeclarations,
        int $maxOutputTokens = 1500,
    ): array {
        $prepared = $this->prependSystemPrompt($contents, $systemPrompt);
        $payload = [
            'contents' => $prepared,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => $maxOutputTokens,
                'topP' => 0.9,
            ],
        ];

        if ($functionDeclarations !== []) {
            $payload['tools'] = [
                ['functionDeclarations' => $functionDeclarations],
            ];
        }

        $response = $this->postGenerateContent($payload);
        $parts = (array) data_get($response->json(), 'candidates.0.content.parts', []);

        $text = '';
        $functionCall = null;

        foreach ($parts as $part) {
            if (! is_array($part)) {
                continue;
            }
            if (($part['thought'] ?? false) === true) {
                continue;
            }
            if (isset($part['functionCall']) && is_array($part['functionCall'])) {
                $functionCall = [
                    'name' => (string) ($part['functionCall']['name'] ?? ''),
                    'args' => (array) ($part['functionCall']['args'] ?? []),
                ];
            }
            if (isset($part['text']) && is_string($part['text'])) {
                $chunk = $part['text'];
                if (str_starts_with(ltrim($chunk), 'THOUGHT:')) {
                    continue;
                }
                $text .= $chunk;
            }
        }

        $text = $this->sanitizer->sanitize(trim($text));

        if ($functionCall !== null && $functionCall['name'] !== '') {
            return ['text' => null, 'functionCall' => $functionCall];
        }

        if ($text === '') {
            throw new RuntimeException('llm_empty_response');
        }

        return ['text' => $text, 'functionCall' => null];
    }

    /**
     * @param list<array{role:string, content:string}> $conversation
     * @throws RuntimeException
     */
    private function requestGemini(string $systemPrompt, array $conversation): string
    {
        $contents = [];
        foreach ($conversation as $turn) {
            $role = ($turn['role'] ?? 'user') === 'model' ? 'model' : 'user';
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '') {
                continue;
            }
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $content]],
            ];
        }
        if ($contents === []) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => 'Xin chào']],
            ];
        }

        $result = $this->generateWithTools($systemPrompt, $contents, [], 1500);

        return (string) ($result['text'] ?? '');
    }

    /**
     * @param list<array<string, mixed>> $contents
     * @return list<array<string, mixed>>
     */
    private function prependSystemPrompt(array $contents, string $systemPrompt): array
    {
        if ($contents === []) {
            return [
                [
                    'role' => 'user',
                    'parts' => [['text' => 'Xin chào']],
                ],
            ];
        }

        $contents = array_values($contents);
        $firstText = (string) data_get($contents, '0.parts.0.text', '');
        $contents[0]['parts'][0]['text'] = trim(
            "Hướng dẫn hệ thống:\n{$systemPrompt}\n\n" .
            "Bắt đầu hội thoại:\n{$firstText}"
        );

        return $contents;
    }

    /**
     * @param array<string, mixed> $payload
     * @throws RuntimeException
     */
    private function postGenerateContent(array $payload): Response
    {
        $primaryKey = (string) config('services.gemini.api_key', '');
        $backupKeysRaw = (string) config('services.gemini.api_keys', '');
        $primaryModel = (string) config('services.gemini.model', 'gemini-2.0-flash');
        $timeout = (int) config('services.gemini.timeout', 20);
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $baseUrls = $this->resolveBaseUrls($baseUrl);
        if (isset($payload['tools'])) {
            $baseUrls = array_values(array_filter(
                $baseUrls,
                static fn (string $url): bool => str_contains($url, 'v1beta'),
            ));
            if ($baseUrls === []) {
                $baseUrls = ['https://generativelanguage.googleapis.com/v1beta'];
            }
        }

        $backupKeys = array_values(array_filter(array_map(
            static fn (string $x): string => trim($x),
            explode(',', $backupKeysRaw)
        )));
        $apiKeys = array_values(array_unique(array_filter([$primaryKey, ...$backupKeys])));

        if ($apiKeys === []) {
            throw new RuntimeException('llm_missing_api_key');
        }

        $models = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
        ])));
        $lastError = 'llm_unavailable';
        $response = null;
        $hasSuccessfulResponse = false;
        $allQuotaExceeded = true;

        foreach ($apiKeys as $apiKey) {
            foreach ($baseUrls as $activeBaseUrl) {
                $resolvedModels = $this->resolveAvailableModels($activeBaseUrl, $apiKey, $models, $timeout);
                foreach ($resolvedModels as $model) {
                    $url = "{$activeBaseUrl}/models/{$model}:generateContent";
                    $response = Http::timeout($timeout)
                        ->acceptJson()
                        ->withQueryParameters(['key' => $apiKey])
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $hasSuccessfulResponse = true;
                        break 3;
                    }

                    if ($response->status() !== 429) {
                        $allQuotaExceeded = false;
                    }

                    $apiMessage = (string) data_get($response->json(), 'error.message', '');
                    $lastError = 'llm_unavailable_status_' . $response->status() . ($apiMessage !== '' ? ('|' . $apiMessage) : '');
                    Log::warning('Gemini request failed', [
                        'base_url' => $activeBaseUrl,
                        'model' => $model,
                        'status' => $response->status(),
                        'body' => $apiMessage !== '' ? $apiMessage : $response->body(),
                    ]);

                    if ($response->status() !== 404 && $response->status() !== 429) {
                        break;
                    }
                }
            }
        }

        if (! $hasSuccessfulResponse || ! $response || ! $response->successful()) {
            if ($allQuotaExceeded) {
                throw new RuntimeException('llm_quota_exceeded');
            }
            throw new RuntimeException($lastError);
        }

        return $response;
    }

    /**
     * @return list<string>
     */
    private function resolveBaseUrls(string $configuredBaseUrl): array
    {
        $urls = [$configuredBaseUrl];
        if (str_contains($configuredBaseUrl, '/v1beta')) {
            $urls[] = str_replace('/v1beta', '/v1', $configuredBaseUrl);
        } elseif (str_contains($configuredBaseUrl, '/v1')) {
            $urls[] = str_replace('/v1', '/v1beta', $configuredBaseUrl);
        }

        return array_values(array_unique(array_filter($urls)));
    }

    /**
     * @param list<string> $preferredModels
     * @return list<string>
     */
    private function resolveAvailableModels(string $baseUrl, string $apiKey, array $preferredModels, int $timeout): array
    {
        $listUrl = "{$baseUrl}/models";
        $res = Http::timeout($timeout)
            ->acceptJson()
            ->withQueryParameters(['key' => $apiKey])
            ->get($listUrl);

        if (! $res->successful()) {
            return $preferredModels;
        }

        $models = (array) data_get($res->json(), 'models', []);
        $available = [];
        foreach ($models as $model) {
            $name = (string) data_get($model, 'name', '');
            $methods = (array) data_get($model, 'supportedGenerationMethods', []);
            if ($name === '' || ! in_array('generateContent', $methods, true)) {
                continue;
            }

            $clean = preg_replace('/^models\//', '', $name) ?? $name;
            if ($clean !== '') {
                $available[] = $clean;
            }
        }

        if ($available === []) {
            return $preferredModels;
        }

        $available = array_values(array_unique($available));
        $prioritized = [];
        foreach ($preferredModels as $candidate) {
            if (in_array($candidate, $available, true)) {
                $prioritized[] = $candidate;
            }
        }

        foreach ($available as $candidate) {
            if (! in_array($candidate, $prioritized, true)) {
                $prioritized[] = $candidate;
            }
        }

        return $prioritized;
    }
}
