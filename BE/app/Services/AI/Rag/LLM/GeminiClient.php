<?php

namespace App\Services\AI\Rag\LLM;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiClient
{
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
     * @param list<array{role:string, content:string}> $conversation
     * @throws RuntimeException
     */
    private function requestGemini(string $systemPrompt, array $conversation): string
    {
        $primaryKey = (string) config('services.gemini.api_key', '');
        $backupKeysRaw = (string) config('services.gemini.api_keys', '');
        $primaryModel = (string) config('services.gemini.model', 'gemini-2.0-flash');
        $timeout = (int) config('services.gemini.timeout', 20);
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $baseUrls = $this->resolveBaseUrls($baseUrl);

        $backupKeys = array_values(array_filter(array_map(
            static fn(string $x): string => trim($x),
            explode(',', $backupKeysRaw)
        )));
        $apiKeys = array_values(array_unique(array_filter([$primaryKey, ...$backupKeys])));

        if ($apiKeys === []) {
            throw new RuntimeException('llm_missing_api_key');
        }

        $contents = [];
        foreach ($conversation as $turn) {
            $role = ($turn['role'] ?? 'user') === 'model' ? 'model' : 'user';
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '') {
                continue;
            }
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $content],
                ],
            ];
        }
        if ($contents === []) {
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => 'Xin chào'],
                ],
            ];
        }

        // Put system prompt into the first user message for wider API compatibility.
        $firstText = (string) data_get($contents, '0.parts.0.text', '');
        $contents[0]['parts'][0]['text'] = trim(
            "Hướng dẫn hệ thống:\n{$systemPrompt}\n\n" .
            "Bắt đầu hội thoại:\n{$firstText}"
        );

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
                        ->post($url, [
                            'contents' => $contents,
                            'generationConfig' => [
                                'temperature' => 0.7,
                                'maxOutputTokens' => 500,
                                'topP' => 0.9,
                            ],
                        ]);

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

                    // Try next model only when model is not found.
                    if ($response->status() !== 404 && $response->status() !== 429) {
                        break;
                    }
                }
            }
        }

        if (!$hasSuccessfulResponse || !$response || !$response->successful()) {
            if ($allQuotaExceeded) {
                throw new RuntimeException('llm_quota_exceeded');
            }
            throw new RuntimeException($lastError);
        }

        $text = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        if (trim($text) === '') {
            throw new RuntimeException('llm_empty_response');
        }

        return trim($text);
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

        if (!$res->successful()) {
            return $preferredModels;
        }

        $models = (array) data_get($res->json(), 'models', []);
        $available = [];
        foreach ($models as $model) {
            $name = (string) data_get($model, 'name', '');
            $methods = (array) data_get($model, 'supportedGenerationMethods', []);
            if ($name === '' || !in_array('generateContent', $methods, true)) {
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
            if (!in_array($candidate, $prioritized, true)) {
                $prioritized[] = $candidate;
            }
        }

        return $prioritized;
    }
}
