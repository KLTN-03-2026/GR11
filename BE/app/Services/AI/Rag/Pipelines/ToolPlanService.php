<?php

namespace App\Services\AI\Rag\Pipelines;

use App\Services\AI\Rag\LLM\GeminiClient;
use Illuminate\Support\Facades\Log;
use Throwable;

class ToolPlanService
{
    public function __construct(
        private readonly GeminiClient $geminiClient
    ) {
    }

    /**
     * @param list<array{name:string,description:string,args:array<string,string>}> $toolDefinitions
     * @param list<array{role:string, content:string}> $history
     * @return array{use_tool:bool,tool_name:?string,args:array<string,mixed>,reason:?string}|null
     */
    public function plan(string $assistantRole, string $message, array $toolDefinitions, array $history): ?array
    {
        if ($toolDefinitions === []) {
            return null;
        }

        $compactHistory = array_slice($history, -6);
        $toolJson = json_encode($toolDefinitions, JSON_UNESCAPED_UNICODE);
        $historyJson = json_encode($compactHistory, JSON_UNESCAPED_UNICODE);
        $prompt = trim(
            "You are a strict function-calling planner.\n" .
            "Role: {$assistantRole}\n" .
            "Available tools(JSON): {$toolJson}\n" .
            "Recent history(JSON): {$historyJson}\n" .
            "User message: {$message}\n\n" .
            "Task:\n" .
            "- Decide whether calling exactly one tool is needed.\n" .
            "- If yes, map user intent to best tool and extract arguments.\n" .
            "- If no suitable tool exists, do not call any tool.\n\n" .
            "Output ONLY valid JSON object in one line:\n" .
            "{\"use_tool\":true|false,\"tool_name\":\"... or null\",\"args\":{},\"reason\":\"short reason\"}"
        );

        try {
            $raw = $this->geminiClient->generateText($prompt);
            $json = $this->extractFirstJsonObject($raw);
            if ($json === null) {
                return null;
            }

            /** @var array<string,mixed>|null $decoded */
            $decoded = json_decode($json, true);
            if (!is_array($decoded)) {
                return null;
            }

            return [
                'use_tool' => (bool) ($decoded['use_tool'] ?? false),
                'tool_name' => isset($decoded['tool_name']) && is_string($decoded['tool_name']) ? $decoded['tool_name'] : null,
                'args' => isset($decoded['args']) && is_array($decoded['args']) ? $decoded['args'] : [],
                'reason' => isset($decoded['reason']) && is_string($decoded['reason']) ? $decoded['reason'] : null,
            ];
        } catch (Throwable $e) {
            // Tại sao: Planner chỉ là lớp định tuyến thông minh; khi lỗi phải hạ cấp an toàn về luồng mặc định.
            Log::warning('ToolPlanService failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function extractFirstJsonObject(string $text): ?string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        return substr($text, $start, $end - $start + 1) ?: null;
    }
}

