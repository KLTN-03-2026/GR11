<?php

namespace App\Services\AI\Rag\Pipelines;

use App\Models\NguoiDung;
use App\Services\AI\Rag\LLM\GeminiClient;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProactiveGreetingService
{
    public function __construct(
        private readonly GreetingContextService $greetingContextService,
        private readonly GeminiClient $geminiClient,
    ) {}

    /**
     * Sinh lời chào chủ động khi user mở chatbox.
     *
     * @return array{message:string, suggestions:list<array{label:string,action:string}>, case:string, context:array<string,mixed>}
     */
    public function greet(NguoiDung $user): array
    {
        $context = $this->greetingContextService->resolve($user);
        $message = $this->generateMessage($context);

        return [
            'message'     => $message,
            'suggestions' => array_values($context['suggestions']),
            'case'        => $context['case'],
            'context'     => $context,
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    private function generateMessage(array $context): string
    {
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt   = $this->buildUserPrompt($context);

        try {
            $response = $this->geminiClient->generateFromConversation(
                $systemPrompt,
                [],
                $userPrompt,
            );

            $response = trim($response);
            if ($response !== '') {
                return $response;
            }
        } catch (Throwable $e) {
            Log::warning('ProactiveGreetingService: Gemini failed', [
                'case'  => $context['case'],
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback cứng nếu Gemini lỗi
        return $this->buildFallbackMessage($context);
    }

    private function buildSystemPrompt(): string
    {
        return <<<PROMPT
Bạn là "Cô giáo ảo" của EchoKids, ứng dụng luyện phát âm tiếng Việt cho trẻ em 5-10 tuổi.
Xưng hô: "cô - con". Giọng thân thiện, vui vẻ, ngắn gọn, khuyến khích.

Nhiệm vụ: Dựa trên dữ liệu học tập được cung cấp, viết 1-2 câu chào hỏi CHỦ ĐỘNG phù hợp với tình huống (case).
Quy tắc:
- Không nói chung chung. Phải đề cập cụ thể tên bài, điểm số, lỗi phát âm nếu có trong dữ liệu.
- Không dùng markdown, không dùng emoji.
- Tối đa 2 câu ngắn.
- Kết thúc bằng một câu mời học tiếp hoặc câu hỏi gợi mở.
PROMPT;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildUserPrompt(array $context): string
    {
        $case            = (string) $context['case'];
        $lastLessonTitle = $context['last_lesson_title'] ? "\"" . $context['last_lesson_title'] . "\"" : null;
        $lastScore       = $context['last_score'] !== null ? (int) $context['last_score'] . "/100" : null;
        $avgScore        = $context['avg_score_recent'] !== null ? round((float) $context['avg_score_recent'], 1) . "/100" : null;
        $daysAbsent      = (int) $context['days_absent'];
        $topError        = $context['top_error'] ? (string) $context['top_error'] : null;
        $nextLesson      = $context['next_lesson_title'] ? "\"" . $context['next_lesson_title'] . "\"" : null;

        return match ($case) {
            'day1' =>
                "Case: Ngày đầu tiên, học viên chưa có dữ liệu học tập nào.\n" .
                "Hãy chào đón và giới thiệu ngắn gọn, mời bắt đầu bài đầu tiên.",

            'day2' =>
                "Case: Học viên quay lại ngày hôm sau.\n" .
                "Bài đã học hôm qua: " . ($lastLessonTitle ?? 'chưa xác định') . ".\n" .
                "Điểm hôm qua: " . ($lastScore ?? 'chưa có') . ".\n" .
                ($nextLesson ? "Bài tiếp theo: {$nextLesson}.\n" : '') .
                "Hãy nhận xét ngắn kết quả hôm qua và mời học tiếp hoặc ôn lại.",

            'case_a' =>
                "Case: Học viên học đều và điểm cao.\n" .
                "Điểm trung bình 5 ngày gần đây: " . ($avgScore ?? 'cao') . ".\n" .
                "Hãy khen ngợi và gợi ý thử thách khó hơn.",

            'case_b' =>
                "Case: Học viên gặp lỗi phát âm.\n" .
                "Nhóm lỗi phổ biến nhất gần đây: " . ($topError ?? 'chưa xác định') . ".\n" .
                ($lastLessonTitle ? "Bài đang học: {$lastLessonTitle}.\n" : '') .
                "Hãy nhắc nhẹ nhàng về lỗi và khuyến khích luyện lại.",

            'case_c' =>
                "Case: Học viên vắng " . $daysAbsent . " ngày.\n" .
                ($lastLessonTitle ? "Bài đang dang dở: {$lastLessonTitle}.\n" : '') .
                "Hãy chào mừng trở lại vui vẻ và mời khởi động nhẹ trước khi tiếp tục.",

            default =>
                "Hãy chào học viên và hỏi hôm nay muốn luyện gì.",
        };
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildFallbackMessage(array $context): string
    {
        $case = (string) $context['case'];
        $lastTitle = $context['last_lesson_title'] ? (string) $context['last_lesson_title'] : null;
        $daysAbsent = (int) $context['days_absent'];
        $topError = $context['top_error'] ? (string) $context['top_error'] : null;

        return match ($case) {
            'day1'   => 'Chào con! Cô là trợ lý EchoKids. Hôm nay mình cùng bắt đầu luyện phát âm tiếng Việt nhé!',
            'day2'   => 'Chào con, hôm qua con học rất chăm chỉ rồi đó!'
                       . ($lastTitle ? " Hôm nay mình tiếp tục sau bài \"{$lastTitle}\" nhé!" : ' Hôm nay mình tiếp tục nhé!'),
            'case_a' => 'Wow, con học đều và điểm cao thế! Hôm nay thử thách khó hơn một chút nhé, cô tin con làm được!',
            'case_b' => 'Chào con! Cô thấy gần đây con còn nhầm một chút ở nhóm ' . ($topError ?? 'phát âm') . '. Hôm nay mình luyện lại cho thật chắc nhé!',
            'case_c' => 'Lâu rồi không gặp con, cô nhớ lắm! Vắng ' . $daysAbsent . ' ngày rồi, hôm nay mình khởi động nhẹ rồi học tiếp nhé!'
                       . ($lastTitle ? " Bài \"{$lastTitle}\" vẫn đang chờ con đó." : ''),
            default  => 'Chào con! Hôm nay con muốn luyện gì, cô sẵn sàng hỗ trợ nhé!',
        };
    }
}
