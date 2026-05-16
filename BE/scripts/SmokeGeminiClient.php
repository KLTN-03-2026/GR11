<?php

namespace Scripts;

use App\Services\AI\Rag\LLM\GeminiClient;

/**
 * Deterministic Gemini stub for local smoke tests when API key is unavailable.
 */
final class FailingSmokeGeminiClient extends GeminiClient
{
    /**
     * @param  list<array<string, mixed>>  $contents
     * @param  list<array<string, mixed>>  $functionDeclarations
     * @return array{text:?string, functionCall:?array{name:string, args:array<string, mixed>}}
     */
    public function generateWithTools(
        string $systemPrompt,
        array $contents,
        array $functionDeclarations,
        int $maxOutputTokens = 1500,
    ): array {
        throw new \RuntimeException('llm_unavailable_status_400|API Key not found. Please pass a valid API key.');
    }

    public function generateText(string $prompt): string
    {
        throw new \RuntimeException('llm_unavailable_status_400|API Key not found. Please pass a valid API key.');
    }
}

final class SmokeGeminiClient extends GeminiClient
{
    /**
     * @param  list<array<string, mixed>>  $contents
     * @param  list<array<string, mixed>>  $functionDeclarations
     * @return array{text:?string, functionCall:?array{name:string, args:array<string, mixed>}}
     */
    public function generateWithTools(
        string $systemPrompt,
        array $contents,
        array $functionDeclarations,
        int $maxOutputTokens = 1500,
    ): array {
        $hasFunctionResponse = $this->hasFunctionResponse($contents);

        if ($hasFunctionResponse) {
            return [
                'text' => 'Smoke test: dữ liệu đã được lấy từ hệ thống.',
                'functionCall' => null,
            ];
        }

        $userText = $this->extractLastUserText($contents);
        $toolName = $this->resolveToolName($userText);

        if ($toolName === null) {
            return [
                'text' => 'Smoke test: không xác định được tool phù hợp.',
                'functionCall' => null,
            ];
        }

        return [
            'text' => null,
            'functionCall' => [
                'name' => $toolName,
                'args' => $this->resolveToolArgs($toolName, $userText),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveToolArgs(string $toolName, string $userText): array
    {
        if ($toolName === 'student_get_teacher_lesson_count') {
            if (preg_match('/(?:cô|co|thầy|thay|giáo viên|giao vien)\s+(.+?)(?:\s+có|\s+co|\?|$)/u', $userText, $m) === 1) {
                return ['teacher_name' => trim($m[1])];
            }

            return ['teacher_name' => 'Thu Hà'];
        }

        return [];
    }

    public function generateText(string $prompt): string
    {
        return 'Smoke test greeting.';
    }

    /**
     * @param  list<array<string, mixed>>  $contents
     */
    private function hasFunctionResponse(array $contents): bool
    {
        foreach ($contents as $turn) {
            foreach ((array) ($turn['parts'] ?? []) as $part) {
                if (isset($part['functionResponse'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  list<array<string, mixed>>  $contents
     */
    private function extractLastUserText(array $contents): string
    {
        $text = '';
        foreach ($contents as $turn) {
            if (($turn['role'] ?? '') !== 'user') {
                continue;
            }
            foreach ((array) ($turn['parts'] ?? []) as $part) {
                if (isset($part['text'])) {
                    $text = (string) $part['text'];
                }
            }
        }

        return mb_strtolower($text, 'UTF-8');
    }

    private function resolveToolName(string $userText): ?string
    {
        if (
            (str_contains($userText, 'bao nhiêu') || str_contains($userText, 'bao nhieu'))
            && str_contains($userText, 'bài học')
            && (str_contains($userText, 'cô') || str_contains($userText, 'thầy') || str_contains($userText, 'giáo viên'))
        ) {
            return 'student_get_teacher_lesson_count';
        }
        if (
            (str_contains($userText, 'giáo viên') || str_contains($userText, 'giao vien'))
            && ! str_contains($userText, 'bài học')
        ) {
            return 'student_get_my_teachers';
        }
        if (str_contains($userText, 'danh mục') || str_contains($userText, 'danh muc') || str_contains($userText, 'chủ đề')) {
            return 'student_get_lesson_categories';
        }
        if (str_contains($userText, 'premium') && (str_contains($userText, 'mua') || str_contains($userText, 'huong dan'))) {
            return str_contains($userText, 'giao vien') || str_contains($userText, 'thay')
                ? 'teacher_get_premium_purchase_guide'
                : 'student_get_premium_purchase_guide';
        }
        if (str_contains($userText, 'điểm') || str_contains($userText, 'tuần qua')) {
            return 'student_get_personal_dashboard_data';
        }
        if (str_contains($userText, 'bài kế') || str_contains($userText, 'bài tiếp')) {
            return 'student_get_next_lesson_recommendation';
        }
        if (str_contains($userText, 'goi y') && str_contains($userText, 'luyen tap')) {
            return 'student_get_teacher_suggestions';
        }
        if (str_contains($userText, 'lỗi phát âm') || str_contains($userText, 'phát âm')) {
            return 'student_get_detailed_pronunciation_errors';
        }
        if (
            (str_contains($userText, 'danh sach') || str_contains($userText, 'danh sách'))
            && (str_contains($userText, 'hoc vien') || str_contains($userText, 'học viên'))
        ) {
            return 'teacher_list_my_students';
        }
        if (str_contains($userText, 'học viên') || str_contains($userText, 'lớp')) {
            return 'teacher_get_class_overview';
        }
        if (
            (str_contains($userText, 'tạo') || str_contains($userText, 'tao'))
            && (str_contains($userText, 'bài học') || str_contains($userText, 'bai hoc'))
            && (str_contains($userText, 'bao nhiêu') || str_contains($userText, 'bao nhieu'))
        ) {
            return 'teacher_get_my_lesson_stats';
        }

        return null;
    }
}
