<?php

namespace App\Services\AI\Rag\Pipelines;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\NguoiDung;
use App\Services\AI\Rag\LLM\GeminiClient;
use App\Services\AI\Rag\NLU\IntentClassifier;
use App\Services\AI\Rag\Prompt\SystemPromptFactory;
use App\Services\AI\Rag\Support\TextNormalizer;
use App\Services\AI\Rag\Tools\StudentLearningTools;
use App\Services\AI\Rag\Tools\StudentDatabaseQueryTools;
use App\Services\AI\Rag\Tools\StudentMessagingTools;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatRagService
{
    public function __construct(
        private readonly IntentClassifier $intentClassifier,
        private readonly SystemPromptFactory $systemPromptFactory,
        private readonly TextNormalizer $normalizer,
        private readonly GeminiClient $geminiClient,
        private readonly StudentLearningTools $studentLearningTools,
        private readonly StudentDatabaseQueryTools $studentDatabaseQueryTools,
        private readonly StudentMessagingTools $studentMessagingTools,
        private readonly ToolPlanService $toolPlanService,
        private readonly RoleRagKnowledgeService $roleRagKnowledgeService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function reply(NguoiDung $user, string $message, ?int $sessionId = null, string $inputType = 'text'): array
    {
        $session = $this->resolveSession($user, $sessionId);
        $history = $this->latestHistory($session->id);
        $assistantRole = $this->resolveAssistantRole($user);
        $intent = $this->intentClassifier->detect($message, $assistantRole);

        $toolReply = $this->tryRoleToolFlow($user, $assistantRole, $message, $session->id);
        if ($toolReply !== null) {
            return $toolReply;
        }

        $ruleReply = $this->resolveSystemFlowReply($message);
        if ($ruleReply !== null) {
            $this->saveTurn($session->id, $message, (string) $ruleReply['message']);
            return [
                'session_id' => $session->id,
                'message' => $ruleReply['message'],
                'action_url' => $ruleReply['action_url'] ?? null,
                'action_label' => $ruleReply['action_label'] ?? null,
                'meta' => [
                    'intent' => 'system_support',
                    'source' => 'rule',
                    'input_type' => $inputType,
                ],
            ];
        }

        $ragReply = $this->roleRagKnowledgeService->tryAnswer($assistantRole, $message);
        if ($ragReply !== null) {
            $this->saveTurn($session->id, $message, (string) $ragReply['message']);
            return [
                'session_id' => $session->id,
                'message' => $ragReply['message'],
                'action_url' => $ragReply['action_url'] ?? null,
                'action_label' => $ragReply['action_label'] ?? null,
                'meta' => [
                    'intent' => $intent,
                    'assistant_role' => $assistantRole,
                    'source' => 'rag',
                    'rag_topic' => $ragReply['topic'] ?? 'general',
                    'input_type' => $inputType,
                ],
            ];
        }

        $systemPrompt = $this->systemPromptFactory->build($assistantRole, $intent, [], $history);
        $answer = 'Cô đang gặp chút trục trặc kết nối. Con gửi lại giúp cô một lần nữa nhé.';
        $source = 'fallback';
        $errorDetail = null;
        $actionUrl = null;
        $actionLabel = null;

        try {
            $llmAnswer = $this->geminiClient->generateFromConversation($systemPrompt, $history, $message);
            $answer = $this->sanitizeLlmOutput($llmAnswer, $history);
            $source = 'gemini';
        } catch (Throwable $e) {
            $errorDetail = (string) $e->getMessage();
            Log::warning('ChatRagService LLM failed', [
                'session_id' => $session->id,
                'user_id' => $user->id,
                'error' => $errorDetail,
            ]);

            $reason = $this->mapLlmExceptionToReason((string) $e->getMessage());
            if (in_array($reason, ['missing_api_key', 'quota', 'timeout', 'unavailable'], true)) {
                $answer = $this->buildOfflineReply($assistantRole, $intent, $message);
                $source = 'degraded_rule';
                [$actionUrl, $actionLabel] = $this->resolveActionFromMessage($message);
            } else {
                $answer = 'Cô đang gặp chút trục trặc kết nối. Con gửi lại giúp cô một lần nữa nhé.';
                $source = config('app.debug') ? 'debug_error' : 'fallback';
            }
        }

        $this->saveTurn($session->id, $message, $answer);

        return [
            'session_id' => $session->id,
            'message' => $answer,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
            'meta' => [
                'intent' => $intent,
                'assistant_role' => $assistantRole,
                'source' => $source,
                'input_type' => $inputType,
                'degraded' => $source === 'degraded_rule',
                'error_detail' => config('app.debug') ? $errorDetail : null,
            ],
        ];
    }

    /**
     * @return array{session_id:int,status:string}
     */
    public function resolveSessionMeta(NguoiDung $user, ?int $sessionId = null): array
    {
        $session = $this->resolveSession($user, $sessionId);
        return [
            'session_id' => $session->id,
            'status' => (string) $session->status,
        ];
    }

    /**
     * @param list<array{role:string, content:string}> $history
     */
    private function sanitizeLlmOutput(string $llmAnswer, array $history): string
    {
        $answer = trim($llmAnswer);
        if ($answer === '') {
            return 'Con nói lại giúp cô 1 câu ngắn hơn nhé.';
        }

        if (mb_strlen($answer, 'UTF-8') > 500) {
            $answer = mb_substr($answer, 0, 500, 'UTF-8');
        }

        $normalized = $this->normalizer->normalize($answer);
        if (str_contains($normalized, 'system prompt') || str_contains($normalized, 'he thong')) {
            return 'Con hỏi lại giúp cô theo cách đơn giản hơn nhé.';
        }

        foreach (array_slice($history, -2) as $row) {
            if (($row['role'] ?? '') !== 'model') {
                continue;
            }
            if ($this->normalizer->normalize((string) ($row['content'] ?? '')) === $normalized) {
                return 'Cô đổi cách nói cho dễ hiểu hơn nhé: con muốn biết thêm chi tiết nào?';
            }
        }

        return $answer;
    }

    private function mapLlmExceptionToReason(string $message): string
    {
        $normalized = $this->normalizer->normalize($message);
        if (str_contains($normalized, 'quota')) {
            return 'quota';
        }
        if (str_contains($normalized, 'missing api key') || str_contains($normalized, 'missing_api_key')) {
            return 'missing_api_key';
        }
        if (str_contains($normalized, 'timed out') || str_contains($normalized, 'timeout')) {
            return 'timeout';
        }

        return 'unavailable';
    }

    private function resolveSession(NguoiDung $user, ?int $sessionId = null): ChatSession
    {
        if ($sessionId !== null) {
            $session = ChatSession::query()
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();
            if ($session !== null) {
                return $session;
            }
        }

        return ChatSession::query()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }

    /**
     * @return list<array{role:string, content:string}>
     */
    public function latestHistoryBySession(NguoiDung $user, int $sessionId, int $limit = 20): array
    {
        $session = ChatSession::query()
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->first();
        if ($session === null) {
            return [];
        }

        return $this->latestHistory($session->id, $limit);
    }

    /**
     * @return list<array{role:string, content:string}>
     */
    private function latestHistory(int $sessionId, int $limit = 10): array
    {
        return ChatMessage::query()
            ->where('session_id', $sessionId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['role', 'content'])
            ->reverse()
            ->map(static fn(ChatMessage $row): array => [
                'role' => (string) $row->role,
                'content' => (string) $row->content,
            ])
            ->values()
            ->all();
    }

    private function saveTurn(int $sessionId, string $userMessage, string $modelMessage): void
    {
        $now = Carbon::now();
        ChatMessage::query()->create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $userMessage,
            'created_at' => $now,
        ]);
        ChatMessage::query()->create([
            'session_id' => $sessionId,
            'role' => 'model',
            'content' => $modelMessage,
            'created_at' => $now,
        ]);
    }

    /**
     * @return array{message:string,action_url:?string,action_label:?string}|null
     */
    private function resolveSystemFlowReply(string $message): ?array
    {
        $normalized = $this->normalizer->normalize($message);
        if ($normalized === '') {
            return [
                'message' => 'Con gửi giúp cô một câu rõ hơn nhé.',
                'action_url' => null,
                'action_label' => null,
            ];
        }
        if (str_contains($normalized, 'doi avatar') || str_contains($normalized, 'anh dai dien')) {
            return [
                'message' => 'Con vào mục Hồ sơ, chọn ảnh đại diện rồi bấm Cập nhật avatar nhé (tại đây). Nếu không thấy nút, con kéo xuống cuối trang Hồ sơ giúp cô.',
                'action_url' => '/profile',
                'action_label' => 'tại đây',
            ];
        }
        if (
            str_contains($normalized, 'doi ten') ||
            str_contains($normalized, 'doi ho ten') ||
            str_contains($normalized, 'cap nhat ten') ||
            str_contains($normalized, 'sua ten')
        ) {
            return [
                'message' => 'Con vào mục Hồ sơ, sửa Họ và tên rồi bấm Cập nhật thông tin nhé (tại đây).',
                'action_url' => '/profile',
                'action_label' => 'tại đây',
            ];
        }
        if (
            str_contains($normalized, 'doi mat khau') ||
            str_contains($normalized, 'thay doi mat khau') ||
            str_contains($normalized, 'quen mat khau')
        ) {
            return [
                'message' => 'Con vào mục Hồ sơ, chọn Đổi mật khẩu rồi nhập mật khẩu cũ và mật khẩu mới nhé (tại đây).',
                'action_url' => '/profile',
                'action_label' => 'tại đây',
            ];
        }
        if (
            str_contains($normalized, 'so dien thoai') ||
            str_contains($normalized, 'them sdt') ||
            str_contains($normalized, 'cap nhat sdt')
        ) {
            return [
                'message' => 'Con vào mục Hồ sơ, cập nhật số điện thoại rồi bấm Cập nhật thông tin nhé (tại đây).',
                'action_url' => '/profile',
                'action_label' => 'tại đây',
            ];
        }
        if (str_contains($normalized, 'loi mang') || str_contains($normalized, 'khong vao duoc')) {
            return [
                'message' => 'Con thử tải lại trang và kiểm tra mạng trước nhé. Nếu vẫn lỗi, đăng xuất rồi đăng nhập lại giúp cô, sau đó thử lại một lần nữa.',
                'action_url' => null,
                'action_label' => null,
            ];
        }
        if (str_contains($normalized, 'man hinh den') || str_contains($normalized, 'den thui')) {
            return [
                'message' => 'Con thử tải lại trang và kiểm tra lại kết nối mạng trước nhé. Nếu vẫn đen màn hình, con đăng xuất rồi đăng nhập lại giúp cô.',
                'action_url' => null,
                'action_label' => null,
            ];
        }
        if (str_contains($normalized, 'am thanh') || str_contains($normalized, 'micro')) {
            return [
                'message' => 'Con mở quyền micro trên trình duyệt, sau đó bấm lại nút mic trong chatbox nhé. Nếu vẫn chưa nghe, con thử khởi động lại trình duyệt.',
                'action_url' => null,
                'action_label' => null,
            ];
        }
        if (str_contains($normalized, 'loa')) {
            return [
                'message' => 'Con kiểm tra âm lượng loa của máy trước, rồi tăng âm trong trình duyệt lên giúp cô nhé.',
                'action_url' => null,
                'action_label' => null,
            ];
        }

        return null;
    }

    private function buildOfflineReply(string $assistantRole, string $intent, string $message): string
    {
        $normalized = $this->normalizer->normalize($message);

        return match ($intent) {
            'greeting' => 'Chào con, cô ở đây nè. Mình bắt đầu nhẹ với 1 câu ngắn để khởi động phát âm nhé.',
            'system_support' => 'Cô đã hiểu vấn đề hệ thống của con rồi. Con làm theo hướng dẫn trên màn hình, nếu chưa được thì nhắn cô thêm 1 ảnh lỗi nhé.',
            'ask_score' => str_contains($normalized, 'cao nhat')
                ? 'Con vào mục Tiến độ để xem điểm cao nhất nhé. Nếu con gửi điểm gần nhất, cô sẽ so sánh giúp con ngay.'
                : 'Hiện cô đang ở chế độ hỗ trợ nhanh. Con vào mục Tiến độ để xem điểm gần nhất nhé, rồi cô phân tích tiếp cho con.',
            'ask_mistake' => 'Mình luyện chậm từng âm tiết nhé. Con gửi cho cô 1 câu con hay đọc sai, cô sẽ hướng dẫn sửa từng bước.',
            'ask_lesson' => 'Con vào mục Bài học, chọn bài mức dễ trước rồi gửi tên bài cho cô, cô sẽ gợi ý cách luyện nhanh nhất.',
            'ask_phonics_path' => 'Cô gợi ý con bắt đầu từ nguyên âm đơn (/a/, /e/, /i/, /o/, /u/) trước nhé, sau đó mới đến phụ âm đơn rồi âm ghép. Luyện theo thứ tự từng nhóm giúp con chắc nền hơn nhiều đó.',
            'ask_pronunciation' => 'Con đọc chậm từng âm tiết giúp cô nhé. Ví dụ từ "nghưỡng": âm đầu "ng" + vần "ưỡng" + thanh sắc, con tách ra rồi ghép lại từng bước.',
            'ask_lesson_switch' => 'Được con nha. Mình đổi sang bài dễ hơn để lấy lại tự tin trước, rồi cô nâng độ khó dần cho con.',
            'ask_parent_report' => 'Phụ huynh vào mục Tiến độ để xem số câu đã luyện và điểm gần đây của bé nhé. Cần thì cô sẽ tóm tắt nhanh theo ngày.',
            'motivation_low' => 'Không sao đâu con, mình nghỉ 2-3 phút rồi quay lại 1 câu thật ngắn nhé. Cô ở đây hỗ trợ con từng bước.',
            'ask_progress' => (str_contains($normalized, 'gio') || str_contains($normalized, 'thoi gian'))
                ? 'Con vào mục Tiến độ để xem tổng thời gian luyện tập nhé. Nếu cần, cô sẽ hướng dẫn đọc chi tiết theo từng ngày.'
                : 'Con mở mục Tiến độ để xem tổng quan trước nhé. Nếu con gửi điểm gần nhất, cô sẽ nhận xét ngay cho con.',
            'practice_chat' => 'Tốt lắm, mình luyện câu này nhé: "Hôm nay con rất vui". Con đọc chậm và rõ từng từ để cô chấm dễ hơn.',
            default => $this->buildGenericOfflineReply($message),
        };
    }

    private function buildGenericOfflineReply(string $message): string
    {
        $normalized = $this->normalizer->normalize($message);
        if (str_contains($normalized, 'avatar') || str_contains($normalized, 'anh dai dien')) {
            return 'Con vào Hồ sơ, chọn ảnh đại diện rồi bấm Cập nhật avatar nhé.';
        }
        if (
            str_contains($normalized, 'doi ten') ||
            str_contains($normalized, 'doi ho ten') ||
            str_contains($normalized, 'cap nhat ten') ||
            str_contains($normalized, 'sua ten')
        ) {
            return 'Con vào Hồ sơ để sửa Họ và tên, rồi bấm Cập nhật thông tin nhé.';
        }
        if (str_contains($normalized, 'bai hoc')) {
            return 'Con vào trang Bài học, chọn bài phù hợp rồi nói cô biết tên bài để cô hướng dẫn cách luyện.';
        }

        return 'Cô chỉ là trợ lý nên chỉ có thể trả lời những gì liên quan đế Echokids không thể trả lời câu hỏi này của con được. Bù lại, cô có thể hướng dẫn con bài học luyện phát âm hôm nay, bài tập luyện âm đầu phù hợp nhất cho ngày hôm nay.';
    }

    private function resolveAssistantRole(NguoiDung $user): string
    {
        return 'client';
    }

    /**
     * @return array<string,mixed>|null
     */
    private function tryRoleToolFlow(NguoiDung $user, string $assistantRole, string $message, int $sessionId): ?array
    {
        try {
            $history = $this->latestHistory($sessionId);
            $toolDefs = $this->toolDefinitionsByRole($assistantRole);
            if ($toolDefs === []) {
                return null;
            }
            // Prefer deterministic keyword routing first to reduce LLM-planner misroutes.
            $plan = $this->detectStudentToolFromMessage($message, $toolDefs);
            if ($plan === null) {
                $llmPlan = $this->toolPlanService->plan($assistantRole, $message, $toolDefs, $history);
                $plan = $this->normalizeToolPlan($llmPlan, $toolDefs);
            }
            if ($plan === null) {
                return null;
            }

            $toolName = (string) $plan['tool'];
            $toolArgs = (array) ($plan['args'] ?? []);
            $result = $this->executeToolByRole($user, $assistantRole, $toolName, $toolArgs);
            $answer = $this->formatToolResultByRole($assistantRole, $toolName, $result);
            $this->saveTurn($sessionId, $message, $answer);

            return [
                'session_id' => $sessionId,
                'message' => $answer,
                'action_url' => null,
                'action_label' => null,
                'meta' => [
                    'intent' => $assistantRole . '_tool',
                    'assistant_role' => $assistantRole,
                    'source' => 'tool',
                    'tool_name' => $toolName,
                    'tool_args' => $toolArgs,
                ],
            ];
        } catch (Throwable $e) {
            Log::warning('ChatRagService tool flow failed', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'assistant_role' => $assistantRole,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    private function toolDefinitionsByRole(string $assistantRole): array
    {
        return array_merge(
            $this->studentLearningTools->definitions(),
            $this->studentDatabaseQueryTools->definitions(),
            $this->studentMessagingTools->definitions()
        );
    }

    /**
     * @param array<string,mixed> $toolArgs
     * @return array<string,mixed>
     */
    private function executeToolByRole(NguoiDung $user, string $assistantRole, string $toolName, array $toolArgs): array
    {
        $messagingToolNames = collect($this->studentMessagingTools->definitions())
            ->pluck('name')
            ->all();
        if (in_array($toolName, $messagingToolNames, true)) {
            return $this->studentMessagingTools->execute($user, $toolName, $toolArgs);
        }

        $databaseToolNames = collect($this->studentDatabaseQueryTools->definitions())
            ->pluck('name')
            ->all();
        if (in_array($toolName, $databaseToolNames, true)) {
            return $this->studentDatabaseQueryTools->execute($user, $toolName, $toolArgs);
        }

        return $this->studentLearningTools->execute($user, $toolName, $toolArgs);
    }

    /**
     * @param array{use_tool:bool,tool_name:?string,args:array<string,mixed>,reason:?string}|null $rawPlan
     * @param list<array{name:string,description:string,args:array<string,string>}> $toolDefinitions
     * @return array{tool:string,args:array<string,mixed>}|null
     */
    private function normalizeToolPlan(?array $rawPlan, array $toolDefinitions): ?array
    {
        if ($rawPlan === null || ($rawPlan['use_tool'] ?? false) !== true) {
            return null;
        }
        $toolName = (string) ($rawPlan['tool_name'] ?? '');
        if ($toolName === '') {
            return null;
        }

        $allowed = collect($toolDefinitions)->pluck('name')->all();
        if (!in_array($toolName, $allowed, true)) {
            return null;
        }

        return [
            'tool' => $toolName,
            'args' => (array) ($rawPlan['args'] ?? []),
        ];
    }

    /**
     * @param list<array{name:string,description:string,args:array<string,string>}> $toolDefinitions
     * @return array{tool:string,args:array<string,mixed>}|null
     */
    private function detectStudentToolFromMessage(string $message, array $toolDefinitions): ?array
    {
        $normalized = $this->normalizer->normalize($message);
        if ($normalized === '') {
            return null;
        }

        $allowed = collect($toolDefinitions)->pluck('name')->all();
        $days = 30;
        if (preg_match('/(\d+)\s*ngay/u', $normalized, $m) === 1) {
            $days = max(1, (int) ($m[1] ?? 30));
        }
        $limit = 5;
        if (preg_match('/top\s*(\d+)/u', $normalized, $m) === 1) {
            $limit = min(10, max(1, (int) ($m[1] ?? 5)));
        }

        $pick = static function (string $tool, array $args = []) use ($allowed): ?array {
            if (!in_array($tool, $allowed, true)) {
                return null;
            }
            return ['tool' => $tool, 'args' => $args];
        };

        if (
            str_contains($normalized, 'bang xep hang')
            || str_contains($normalized, 'dung nhat')
            || str_contains($normalized, 'top learner')
            || str_contains($normalized, 'top hoc vien')
            || (str_contains($normalized, 'top') && str_contains($normalized, 'nguoi'))
            || (str_contains($normalized, 'top') && str_contains($normalized, 'diem cao nhat'))
        ) {
            return $pick('student_get_top_learners_public', ['limit' => $limit]);
        }

        if (
            str_contains($normalized, 'goi y bai')
            || str_contains($normalized, 'nen hoc bai gi')
            || str_contains($normalized, 'hoc bai gi')
            || str_contains($normalized, 'bai hoc tiep')
            || str_contains($normalized, 'bai hoc hom nay')
            || str_contains($normalized, 'goi y hom nay')
            || str_contains($normalized, 'bai phu hop hom nay')
            || str_contains($normalized, 'nen hoc gi hom nay')
            || str_contains($normalized, 'bai nao phu hop')
            || str_contains($normalized, 'de xuat bai')
        ) {
            return $pick('student_get_suggested_lessons_by_level', ['days' => min($days, 30), 'limit' => $limit]);
        }

        if (
            str_contains($normalized, 'lo trinh')
            || str_contains($normalized, 'tuan hien tai')
            || str_contains($normalized, 'moc tiep theo')
        ) {
            return $pick('student_get_learning_path_progress');
        }

        if (
            str_contains($normalized, 'loi phat am')
            || str_contains($normalized, 'sai am')
            || str_contains($normalized, 'loi am dau')
            || str_contains($normalized, 'loi van')
            || str_contains($normalized, 'loi thanh')
        ) {
            return $pick('student_get_detailed_pronunciation_errors', ['days' => $days, 'limit' => 10]);
        }

        if (
            str_contains($normalized, 'tien do')
            || str_contains($normalized, 'so phien')
            || str_contains($normalized, 'tong thoi gian')
            || str_contains($normalized, 'diem trung binh')
            || str_contains($normalized, 'diem cao nhat')
        ) {
            return $pick('student_get_personal_dashboard_data', ['days' => $days]);
        }

        if (
            str_contains($normalized, 'phat am chu')
            || str_contains($normalized, 'phat am tu')
            || str_contains($normalized, 'chu nay doc')
            || str_contains($normalized, 'tu nay doc')
            || str_contains($normalized, 'doc la gi')
            || str_contains($normalized, 'doc sao')
        ) {
            $query = '';
            // Match from the original message to preserve diacritics
            if (preg_match('/(?:chữ|từ)\s+([^\s?]+)/iu', $message, $m)) {
                $query = $m[1];
            } elseif (preg_match('/([^\s?]+)\s+đọc là gì/iu', $message, $m)) {
                $query = $m[1];
            } else {
                // fallback extraction from normalized if exact match fails
                $words = explode(' ', $normalized);
                $query = end($words); 
            }
            return $pick('student_search_vocabulary', ['query' => $query]);
        }

        return null;
    }

    /**
     * @param array<string,mixed> $result
     */
    private function formatToolResultByRole(string $assistantRole, string $toolName, array $result): string
    {
        if (($result['ok'] ?? false) !== true) {
            return (string) ($result['message'] ?? 'Hệ thống chưa lấy được dữ liệu phù hợp, bạn thử hỏi lại rõ hơn nhé.');
        }

        if ($toolName === 'student_get_my_progress_summary') {
            $data = (array) ($result['data'] ?? []);
            return "Trong {$data['days']} ngày gần đây, con có {$data['session_count']} phiên luyện, điểm trung bình {$data['avg_score']}/100, điểm cao nhất {$data['best_score']}/100.";
        }
        if ($toolName === 'student_get_my_practice_suggestion') {
            $data = (array) ($result['data'] ?? []);
            return "Gợi ý cho con trong {$data['days']} ngày tới: {$data['focus']}. Mình luyện đều mỗi ngày 10-15 phút để tiến bộ nhanh nhé.";
        }
        if ($toolName === 'student_get_detailed_pronunciation_errors') {
            $data = (array) ($result['data'] ?? []);
            $errors = (array) ($data['error_categories'] ?? []);
            return "Trong {$data['period_days']} ngày gần đây, tổng lỗi phát âm là {$data['total_errors']} lỗi: âm đầu {$errors['initial_sounds']}, vần {$errors['finals']}, thanh điệu {$errors['tones']}. Nhóm lỗi nhiều nhất là {$data['most_common_error']}.";
        }
        if ($toolName === 'student_get_suggested_lessons_by_level') {
            $data = (array) ($result['data'] ?? []);
            $lessonRows = collect((array) ($data['suggested_lessons'] ?? []))
                ->take(3)
                ->map(static fn(array $row): string => (string) ($row['title'] ?? 'Bài học'))
                ->implode(', ');
            $lessonText = $lessonRows !== '' ? $lessonRows : 'chưa có bài phù hợp ngay lúc này';
            return "Mức hiện tại của con là {$data['current_level']} (điểm TB {$data['current_avg_score']}/100). Cô gợi ý các bài: {$lessonText}.";
        }
        if ($toolName === 'student_get_learning_path_progress') {
            $data = (array) ($result['data'] ?? []);
            if (($data['has_path'] ?? false) !== true) {
                return 'Con chưa có lộ trình cá nhân, cô sẽ tạm gợi ý bài theo điểm luyện gần đây nhé.';
            }

            return "Lộ trình của con đang ở tuần {$data['current_week']}/{$data['total_weeks']} ({$data['progress_percentage']}%). Mốc tiếp theo là: {$data['next_milestone']}.";
        }
        if ($toolName === 'student_get_session_history_with_details') {
            $data = (array) ($result['data'] ?? []);
            return "Trong {$data['period_days']} ngày vừa qua, con có {$data['total_sessions']} phiên luyện. Nếu muốn, cô có thể phân tích sâu từng phiên gần nhất cho con.";
        }
        if ($toolName === 'student_get_pronunciation_progress') {
            $data = (array) ($result['data'] ?? []);
            $trendMap = [
                'improving' => 'đang cải thiện',
                'declining' => 'đang giảm nhẹ',
                'stable' => 'đang ổn định',
            ];
            $trend = $trendMap[(string) ($data['trend'] ?? 'stable')] ?? 'đang ổn định';
            return "Tiến trình phát âm {$data['period_days']} ngày gần đây của con {$trend}. Con cứ giữ nhịp luyện đều mỗi ngày nhé.";
        }
        if ($toolName === 'student_get_vocabulary_progress') {
            $data = (array) ($result['data'] ?? []);
            return "Trong {$data['period_days']} ngày gần đây, con đã luyện {$data['vocabulary_items_practiced']} từ vựng, trung bình {$data['avg_per_week']} từ mỗi tuần.";
        }
        if ($toolName === 'student_get_personal_dashboard_data') {
            $data = (array) ($result['data'] ?? []);
            $practice = (array) ($data['practice'] ?? []);
            $errors = (array) ($data['errors'] ?? []);
            $chat = (array) ($data['chat'] ?? []);
            return "Tổng quan {$data['days']} ngày của con: {$practice['total_sessions']} phiên luyện, điểm TB {$practice['avg_score']}/100, điểm cao nhất {$practice['best_score']}/100. Lỗi phát âm: âm đầu {$errors['loi_am_dau']}, vần {$errors['loi_van']}, thanh điệu {$errors['loi_thanh_dieu']}. Hoạt động chat: {$chat['total_sessions']} phiên, {$chat['total_messages']} tin.";
        }
        if ($toolName === 'student_get_access_scope') {
            $data = (array) ($result['data'] ?? []);
            return "Tài khoản của con có vai trò {$data['role_id']} với {$data['permission_count']} quyền truy cập trong hệ thống.";
        }
        if ($toolName === 'student_get_top_learners_public') {
            $rows = collect((array) ($result['data'] ?? []))
                ->take(3)
                ->map(static fn(array $r): string => '#' . ((int) ($r['rank'] ?? 0)) . ' (' . ((float) ($r['total_hours'] ?? 0)) . ' giờ)')
                ->implode(', ');
            $topOne = collect((array) ($result['data'] ?? []))->first();
            $leader = $topOne ? '#' . ((int) ($topOne['rank'] ?? 1)) . ' (' . ((float) ($topOne['total_hours'] ?? 0)) . ' giờ)' : null;
            return $rows !== ''
                ? "Hiện tại người đứng nhất bảng xếp hạng công khai là {$leader}. Top hiện tại: {$rows}."
                : 'Hiện chưa có dữ liệu bảng xếp hạng công khai.';
        }
        if ($toolName === 'student_get_teacher_messages') {
            $data = (array) ($result['data'] ?? []);
            return "Con có {$data['message_count']} tin nhắn từ giáo viên trong thời gian gần đây.";
        }
        if ($toolName === 'student_get_assigned_teachers') {
            $data = (array) ($result['data'] ?? []);
            return "Con đang được hỗ trợ bởi {$data['teacher_count']} giáo viên.";
        }
        if ($toolName === 'student_send_message_to_teacher') {
            return (string) ($result['message'] ?? 'Tin nhắn đã được gửi tới giáo viên.');
        }
        if ($toolName === 'student_search_vocabulary') {
            $data = (array) ($result['data'] ?? []);
            if (($data['found'] ?? false) !== true) {
                return (string) ($result['data']['message'] ?? 'Cô không tìm thấy từ này trong dữ liệu hệ thống.');
            }
            $results = (array) ($data['results'] ?? []);
            $first = $results[0] ?? null;
            if ($first) {
                return "Chữ \"{$first['tu_chuan']}\" phát âm là \"{$first['phien_am']}\" con nhé. Con há miệng nhỏ, phát âm ngắn gọn nha.";
            }
        }

        return 'Hệ thống đã xử lý yêu cầu bằng tool phù hợp.';
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private function resolveActionFromMessage(string $message): array
    {
        $normalized = $this->normalizer->normalize($message);
        if (str_contains($normalized, 'avatar') || str_contains($normalized, 'anh dai dien')) {
            return ['/profile', 'tại đây'];
        }
        if (
            str_contains($normalized, 'doi ten') ||
            str_contains($normalized, 'doi ho ten') ||
            str_contains($normalized, 'cap nhat ten') ||
            str_contains($normalized, 'sua ten') ||
            str_contains($normalized, 'doi mat khau') ||
            str_contains($normalized, 'so dien thoai') ||
            str_contains($normalized, 'chinh sua ho so')
        ) {
            return ['/profile', 'tại đây'];
        }
        if (str_contains($normalized, 'tien do') || str_contains($normalized, 'lich su hoc tap') || str_contains($normalized, 'da hoc bai nao')) {
            return ['/tien-do', 'tại đây'];
        }
        if (str_contains($normalized, 'thoi gian luyen tap') || str_contains($normalized, 'bao nhieu gio')) {
            return ['/tien-do', 'tại đây'];
        }
        if (str_contains($normalized, 'diem cao nhat') || str_contains($normalized, 'bao nhieu diem') || str_contains($normalized, 'score')) {
            return ['/tien-do', 'tại đây'];
        }
        if (str_contains($normalized, 'bai hoc') || str_contains($normalized, 'doi bai') || str_contains($normalized, 'bai khac')) {
            return ['/bai-hoc', 'tại đây'];
        }

        return [null, null];
    }
}
