<?php

namespace App\Services\AI\Rag\LLM;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OllamaClient implements LlmClientInterface
{
    /**
     * @throws RuntimeException
     */
    public function generateText(string $prompt): string
    {
        $result = $this->chat([
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'tools' => [],
        ], (int) config('services.ollama.max_output_tokens', 512));

        return (string) ($result['text'] ?? '');
    }

    /**
     * @param list<array<string, mixed>> $contents
     * @param list<array<string, mixed>> $functionDeclarations
     * @return array{text:?string, functionCall:?array{name:string, args:array<string, mixed>}}
     */
    public function generateWithTools(
        string $systemPrompt,
        array $contents,
        array $functionDeclarations,
        int $maxOutputTokens = 1500,
    ): array {
        $messages = $this->contentsToMessages($systemPrompt, $contents);
        $tools = $this->toOllamaTools($functionDeclarations);

        return $this->chat([
            'messages' => $messages,
            'tools' => $tools,
        ], $maxOutputTokens);
    }

    /**
     * @param array{messages:list<array<string,mixed>>, tools:list<array<string,mixed>>} $payload
     * @return array{text:?string, functionCall:?array{name:string, args:array<string, mixed>}}
     */
    private function chat(array $payload, int $maxOutputTokens): array
    {
        $baseUrl = rtrim((string) config('services.ollama.base_url', 'http://127.0.0.1:11434'), '/');
        $model = (string) config('services.ollama.model', 'qwen2.5:7b-instruct');
        $timeout = (int) config('services.ollama.timeout', 120);

        $body = [
            'model' => $model,
            'messages' => $payload['messages'],
            'stream' => false,
            'options' => [
                'temperature' => 0.4,
                'num_predict' => $maxOutputTokens,
            ],
        ];

        if ($payload['tools'] !== []) {
            $body['tools'] = $payload['tools'];
        }

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->post("{$baseUrl}/api/chat", $body);
        } catch (\Throwable $e) {
            Log::warning('Ollama connection failed', ['error' => $e->getMessage()]);
            throw new RuntimeException('llm_unavailable|Cannot connect to Ollama. Run: ollama serve');
        }

        if (! $response->successful()) {
            $message = (string) data_get($response->json(), 'error', $response->body());
            Log::warning('Ollama request failed', [
                'status' => $response->status(),
                'body' => $message,
                'model' => $model,
            ]);
            throw new RuntimeException('llm_unavailable_status_' . $response->status() . '|' . $message);
        }

        $message = (array) data_get($response->json(), 'message', []);
        $toolCalls = (array) ($message['tool_calls'] ?? []);

        if ($toolCalls !== []) {
            $first = (array) ($toolCalls[0] ?? []);
            $function = (array) ($first['function'] ?? []);
            $name = (string) ($function['name'] ?? '');
            $args = $function['arguments'] ?? [];
            if (is_string($args)) {
                $decoded = json_decode($args, true);
                $args = is_array($decoded) ? $decoded : [];
            }
            if (! is_array($args)) {
                $args = [];
            }

            if ($name !== '') {
                return [
                    'text' => null,
                    'functionCall' => [
                        'name' => $name,
                        'args' => $args,
                    ],
                ];
            }
        }

        $text = trim((string) ($message['content'] ?? ''));
        if ($text === '') {
            throw new RuntimeException('llm_empty_response');
        }

        return ['text' => $text, 'functionCall' => null];
    }

    /**
     * @param list<array<string, mixed>> $contents
     * @return list<array<string, mixed>>
     */
    private function contentsToMessages(string $systemPrompt, array $contents): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($contents as $turn) {
            if (! is_array($turn)) {
                continue;
            }

            $geminiRole = (string) ($turn['role'] ?? 'user');
            $parts = (array) ($turn['parts'] ?? []);

            foreach ($parts as $part) {
                if (! is_array($part)) {
                    continue;
                }

                if (isset($part['text']) && is_string($part['text']) && trim($part['text']) !== '') {
                    $messages[] = [
                        'role' => $geminiRole === 'model' ? 'assistant' : 'user',
                        'content' => trim($part['text']),
                    ];
                }

                if (isset($part['functionCall']) && is_array($part['functionCall'])) {
                    $name = (string) ($part['functionCall']['name'] ?? '');
                    $args = (array) ($part['functionCall']['args'] ?? []);
                    if ($name !== '') {
                        $messages[] = [
                            'role' => 'assistant',
                            'content' => '',
                            'tool_calls' => [
                                [
                                    'function' => [
                                        'name' => $name,
                                        'arguments' => (object) $args,
                                    ],
                                ],
                            ],
                        ];
                    }
                }

                if (isset($part['functionResponse']) && is_array($part['functionResponse'])) {
                    $name = (string) ($part['functionResponse']['name'] ?? 'tool');
                    $response = $part['functionResponse']['response'] ?? [];
                    $messages[] = [
                        'role' => 'tool',
                        'name' => $name,
                        'content' => json_encode($response, JSON_UNESCAPED_UNICODE),
                    ];
                }
            }
        }

        if (count($messages) === 1) {
            $messages[] = ['role' => 'user', 'content' => 'Xin chào'];
        }

        return $messages;
    }

    /**
     * @param list<array<string, mixed>> $functionDeclarations
     * @return list<array<string, mixed>>
     */
    private function toOllamaTools(array $functionDeclarations): array
    {
        $tools = [];
        foreach ($functionDeclarations as $def) {
            if (! is_array($def)) {
                continue;
            }
            $name = (string) ($def['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $properties = [];
            $required = [];
            $geminiProps = (array) data_get($def, 'parameters.properties', []);
            foreach ($geminiProps as $propName => $propDef) {
                if (! is_array($propDef)) {
                    continue;
                }
                $type = strtolower((string) ($propDef['type'] ?? 'string'));
                $properties[(string) $propName] = [
                    'type' => match ($type) {
                        'integer' => 'integer',
                        'number' => 'number',
                        'boolean' => 'boolean',
                        default => 'string',
                    },
                    'description' => (string) ($propDef['description'] ?? ''),
                ];
            }

            $parameters = ['type' => 'object', 'properties' => (object) $properties];
            if ($properties !== []) {
                $parameters['required'] = array_keys($properties);
            }

            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => $name,
                    'description' => (string) ($def['description'] ?? ''),
                    'parameters' => $parameters,
                ],
            ];
        }

        return $tools;
    }
}
