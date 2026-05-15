<?php

namespace App\Services\AI\Rag\LLM;

interface LlmClientInterface
{
    /**
     * @throws \RuntimeException
     */
    public function generateText(string $prompt): string;

    /**
     * Tool-capable chat completion (Gemini-shaped contents for compatibility).
     *
     * @param list<array<string, mixed>> $contents
     * @param list<array<string, mixed>> $functionDeclarations
     * @return array{text:?string, functionCall:?array{name:string, args:array<string, mixed>}}
     * @throws \RuntimeException
     */
    public function generateWithTools(
        string $systemPrompt,
        array $contents,
        array $functionDeclarations,
        int $maxOutputTokens = 1500,
    ): array;
}
