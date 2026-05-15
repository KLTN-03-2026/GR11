<?php

namespace App\Services\AI\Rag\Support;

/**
 * Strips model chain-of-thought / planning text from user-visible chat replies.
 */
final class LlmResponseSanitizer
{
    public function sanitize(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return $text;
        }

        $text = preg_replace('/^THOUGHT:\s*/mi', '', $text) ?? $text;

        if ($this->hasVisibleReasoning($text)) {
            $extracted = $this->extractUserFacingAnswer($text);
            if ($extracted !== '') {
                return $extracted;
            }
        }

        return trim($text);
    }

    private function hasVisibleReasoning(string $text): bool
    {
        $markers = [
            'The user is asking',
            'I have already called',
            'I need to answer',
            'Response structure:',
            'The data shows:',
            'function call',
            'Primary path:',
        ];

        foreach ($markers as $marker) {
            if (stripos($text, $marker) !== false) {
                return true;
            }
        }

        return false;
    }

    private function extractUserFacingAnswer(string $text): string
    {
        if (preg_match('/(?:^|\R)((?:Con |Cô |Mình |Thầy cô ).+)$/us', $text, $matches) === 1) {
            return trim((string) ($matches[1] ?? ''));
        }

        $lines = preg_split('/\R/', $text) ?: [];
        $buffer = [];
        $capturing = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                if ($capturing && $buffer !== []) {
                    $buffer[] = '';
                }

                continue;
            }

            if (! $capturing && $this->isAnswerStartLine($trimmed)) {
                $capturing = true;
            }

            if (! $capturing) {
                continue;
            }

            if ($this->isReasoningLine($trimmed)) {
                break;
            }

            $buffer[] = $trimmed;
        }

        $joined = trim(implode("\n", $buffer));
        if ($joined !== '') {
            return $joined;
        }

        $paragraphs = preg_split('/\R\s*\R/', $text) ?: [];
        $candidates = [];
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '' || $this->isReasoningLine($paragraph)) {
                continue;
            }
            if ($this->looksLikeVietnameseReply($paragraph)) {
                $candidates[] = $paragraph;
            }
        }

        if ($candidates !== []) {
            return (string) end($candidates);
        }

        return '';
    }

    private function isAnswerStartLine(string $line): bool
    {
        return (bool) preg_match('/^(Con |Cô |Mình |Thầy cô )/u', $line);
    }

    private function isReasoningLine(string $line): bool
    {
        return (bool) preg_match(
            '/^(The user |I have |I need |Response structure|The data |Primary path:|Progress:|Completed lesson:|Next lesson\/milestone:|\d+\.)/i',
            $line,
        );
    }

    private function looksLikeVietnameseReply(string $paragraph): bool
    {
        if ($this->isReasoningLine($paragraph)) {
            return false;
        }

        if (! preg_match('/[àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđ]/ui', $paragraph)) {
            return false;
        }

        return $this->isAnswerStartLine($paragraph)
            || ! preg_match('/\b(the user|i have|i need|response structure)\b/i', $paragraph);
    }
}
