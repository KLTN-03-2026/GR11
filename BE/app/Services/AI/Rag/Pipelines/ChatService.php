<?php

namespace App\Services\AI\Rag\Pipelines;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\NguoiDung;
use App\Services\AI\Rag\LLM\LlmClientInterface;
use App\Services\AI\Rag\Support\LlmResponseSanitizer;
use App\Services\AI\Rag\Support\PremiumFeatureService;
use App\Services\AI\Rag\Support\TextNormalizer;
use App\Services\AI\Rag\Tools\PremiumGuideTools;
use App\Services\AI\Rag\Tools\PremiumReportTools;
use App\Services\AI\Rag\Tools\StudentDatabaseQueryTools;
use App\Services\AI\Rag\Tools\StudentMessagingTools;
use App\Services\AI\Rag\Tools\StudentProgressCatalogTools;
use App\Services\AI\Rag\Tools\StudentRelationshipTools;
use App\Services\AI\Rag\Tools\TeacherClassTools;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ChatService
{
    private const SYSTEM_PROMPT_STUDENT = <<<'TXT'
Bạn là trợ lý học tập của EchoKids. Xưng "cô", gọi user là "con".
- User đang chat là HỌC VIÊN (không phải trợ lý). Khi hỏi "tôi là ai"/tên: trả lời theo [Người dùng đang chat], không yêu cầu ID hay bước xác minh.
- LUÔN dùng function call để lấy dữ liệu thực tế trước khi trả lời câu hỏi về điểm/lộ trình/giáo viên/số bài học của giáo viên/danh mục bài học/lịch sử.
- KHÔNG được bịa số liệu. Nếu function trả ok=false, nói thật là chưa có dữ liệu.
- KHÔNG in suy nghĩ/kế hoạch bằng tiếng Anh. Chỉ trả lời trực tiếp cho học viên bằng tiếng Việt (tối đa 3 câu).
- Khi hỏi mua/nâng cấp Premium: dùng function hướng dẫn mua gói, hướng dẫn qua trang Hồ sơ và ví EchoKids.
TXT;

    private const SYSTEM_PROMPT_TEACHER = <<<'TXT'
Bạn là trợ lý giảng dạy của EchoKids. Xưng "mình", gọi user là "thầy/cô".
- User đang chat là GIÁO VIÊN. Khi hỏi "tôi là ai"/tên: trả lời theo [Người dùng đang chat], không yêu cầu ID học viên hay bước xác minh.
- LUÔN dùng function call cho dữ liệu lớp/học viên/số bài học thầy cô đã tạo.
- Khi hỏi mua/nâng cấp Premium: dùng function hướng dẫn mua gói (trang Hồ sơ, ví, nút Mua bằng số dư ví).
- KHÔNG được bịa số liệu. Nếu function trả ok=false, nói thật là chưa có dữ liệu.
- Trả lời ngắn gọn, không bịa.
TXT;

    public function __construct(
        private readonly LlmClientInterface $llm,
        private readonly StudentDatabaseQueryTools $studentDb,
        private readonly StudentMessagingTools $studentMsg,
        private readonly StudentProgressCatalogTools $studentCatalog,
        private readonly StudentRelationshipTools $studentRel,
        private readonly TeacherClassTools $teacher,
        private readonly PremiumReportTools $premium,
        private readonly PremiumGuideTools $premiumGuide,
        private readonly PremiumFeatureService $premiumGuard,
        private readonly TextNormalizer $normalizer,
        private readonly LlmResponseSanitizer $responseSanitizer,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function reply(NguoiDung $user, string $message, ?int $sessionId = null, string $inputType = 'text'): array
    {
        $session = $this->resolveSession($user, $sessionId);
        $history = $this->latestHistory($session->id);
        $role = $this->resolveRole($user);
        $systemPrompt = $this->buildContextualSystemPrompt($user, $role);
        $toolDefs = $this->buildTools($user, $role);
        $declarations = $this->toGeminiFunctionDeclarations($toolDefs);

        $answer = $role === 'teacher'
            ? 'Mình đang gặp chút trục trặc kết nối. Thầy cô gửi lại giúp mình một lần nữa nhé.'
            : 'Cô đang gặp chút trục trặc kết nối. Con gửi lại giúp cô một lần nữa nhé.';
        $toolsUsed = [];
        $actionUrl = null;
        $actionLabel = null;
        $source = 'fallback';
        $errorDetail = null;

        $normalizedMessage = $this->normalizer->normalize($message);
        $rawLowerMessage = mb_strtolower(trim($message), 'UTF-8');
        $english = $this->prefersEnglish($message);

        if ($this->isPremiumPurchaseQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->tryPremiumPurchaseGuideFallback($user, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => (string) ($direct['action_url'] ?? '/profile'),
                    'action_label' => $english ? 'here' : 'tại đây',
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        if ($this->isSelfIdentityQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->trySelfIdentityFallback($user, $role, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => $role === 'student' ? '/profile' : '/teacher/profile',
                    'action_label' => $english ? 'here' : 'tại đây',
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        if ($role === 'student' && $this->isTeacherLessonCountQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->tryTeacherLessonCountFallback($user, $message, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => null,
                    'action_label' => null,
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        if ($role === 'student' && $this->isStudentLearningPathQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->tryLearningPathFallback($user, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => '/lo-trinh',
                    'action_label' => $english ? 'here' : 'tại đây',
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        if ($role === 'student' && $this->isStudentLearningInventoryQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->tryLearningInventoryFallback($user, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => '/bai-hoc',
                    'action_label' => $english ? 'here' : 'tại đây',
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        if ($role === 'teacher' && $this->isTeacherOwnLessonCountQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->tryTeacherOwnLessonCountFallback($user, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => null,
                    'action_label' => null,
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        if ($role === 'teacher' && $this->isTeacherStudentListQuestion($normalizedMessage, $rawLowerMessage)) {
            $direct = $this->tryTeacherStudentListFallback($user, $english);
            if ($direct !== null) {
                $this->saveTurn($session->id, $message, (string) $direct['message']);

                return [
                    'session_id' => $session->id,
                    'message' => (string) $direct['message'],
                    'action_url' => '/teacher/quan-ly-hoc-sinh',
                    'action_label' => $english ? 'here' : 'tại đây',
                    'meta' => [
                        'assistant_role' => $role,
                        'source' => 'db_direct',
                        'llm_provider' => $this->llmProvider(),
                        'input_type' => $inputType,
                        'tools_used' => (array) ($direct['tools_used'] ?? []),
                        'error_detail' => null,
                    ],
                ];
            }
        }

        try {
            $contents = $this->buildGeminiContents($history, $message);

            for ($round = 0; $round < 3; $round++) {
                $response = $this->llm->generateWithTools($systemPrompt, $contents, $declarations);
                $functionCall = $response['functionCall'] ?? null;

                if ($functionCall !== null && ($functionCall['name'] ?? '') !== '') {
                    $toolName = (string) $functionCall['name'];
                    $toolArgs = (array) ($functionCall['args'] ?? []);
                    $result = $this->dispatchTool($user, $role, $toolName, $toolArgs);
                    $toolsUsed[] = ['name' => $toolName, 'args' => $toolArgs];

                    if ($actionUrl === null && ! empty($result['action_url'])) {
                        $actionUrl = (string) $result['action_url'];
                        $actionLabel = 'tại đây';
                    }

                    $contents[] = [
                        'role' => 'model',
                        'parts' => [
                            ['functionCall' => ['name' => $toolName, 'args' => (object) $toolArgs]],
                        ],
                    ];
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [
                            ['functionResponse' => [
                                'name' => $toolName,
                                'response' => $result,
                            ]],
                        ],
                    ];

                    continue;
                }

                $text = $this->responseSanitizer->sanitize(trim((string) ($response['text'] ?? '')));
                if ($text !== '') {
                    $answer = $text;
                    $source = $toolsUsed !== [] ? $this->llmToolSource() : $this->llmTextSource();
                    break;
                }
            }
        } catch (Throwable $e) {
            $errorDetail = $e->getMessage();
            Log::warning('ChatService LLM failed', [
                'session_id' => $session->id,
                'user_id' => $user->id,
                'error' => $errorDetail,
            ]);
        }

        if ($source === 'fallback') {
            $dbFallback = $this->tryDatabaseFallback($user, $role, $message);
            if ($dbFallback !== null) {
                $answer = (string) $dbFallback['message'];
                $source = (string) $dbFallback['source'];
                $toolsUsed = (array) ($dbFallback['tools_used'] ?? []);
                if (! empty($dbFallback['action_url'])) {
                    $actionUrl = (string) $dbFallback['action_url'];
                    $actionLabel = (string) ($dbFallback['action_label'] ?? 'tại đây');
                }
            }
        }

        $this->saveTurn($session->id, $message, $answer);

        return [
            'session_id' => $session->id,
            'message' => $answer,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
            'meta' => [
                'assistant_role' => $role,
                'source' => $source,
                'llm_provider' => $this->llmProvider(),
                'input_type' => $inputType,
                'tools_used' => $toolsUsed,
                'error_detail' => config('app.debug') ? $errorDetail : null,
            ],
        ];
    }

    /**
     * @return array{message:string,suggestions:list<string>,case:string,digest:null}
     */
    public function greet(NguoiDung $user): array
    {
        $role = $this->resolveRole($user);
        $roleHint = $role === 'teacher' ? 'giáo viên' : 'học viên';
        $prompt = "Hãy chào {$user->ho_ten} ({$roleHint}) bằng 1 câu thân thiện, gợi ý 1 hành động học tập ngắn.";

        try {
            $text = $this->responseSanitizer->sanitize($this->llm->generateText($prompt));
        } catch (Throwable) {
            $text = $role === 'teacher'
                ? "Chào {$user->ho_ten}! Mình sẵn sàng hỗ trợ thầy cô xem lớp và học viên."
                : "Chào {$user->ho_ten}! Cô sẵn sàng giúp con ôn bài và xem tiến độ.";
        }

        return [
            'message' => $text,
            'suggestions' => [],
            'case' => 'simple',
            'digest' => null,
            'context' => ['digest' => null],
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
     * @return list<array{role:string, content:string}>
     */
    public function latestHistoryBySession(NguoiDung $user, int $sessionId, int $limit = 20): array
    {
        $session = ChatSession::query()
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->whereNull('lesson_id')
            ->first();

        if ($session === null) {
            return [];
        }

        return $this->latestHistory($session->id, $limit);
    }

    private function resolveSession(NguoiDung $user, ?int $sessionId = null): ChatSession
    {
        if ($sessionId !== null) {
            $session = ChatSession::query()
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->whereNull('lesson_id')
                ->first();
            if ($session !== null) {
                return $session;
            }
        }

        return ChatSession::query()->create([
            'user_id' => $user->id,
            'lesson_id' => null,
            'status' => 'active',
        ]);
    }

    /**
     * @return list<array{role:string, content:string}>
     */
    private function latestHistory(int $sessionId, int $limit = 10): array
    {
        return ChatMessage::query()
            ->where('session_id', $sessionId)
            ->whereIn('role', ['user', 'model'])
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['role', 'content'])
            ->reverse()
            ->map(static fn (ChatMessage $row): array => [
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

    private function resolveRole(NguoiDung $user): string
    {
        return ((int) $user->vai_tro_id) === NguoiDung::ROLE_TEACHER ? 'teacher' : 'student';
    }

    private function buildContextualSystemPrompt(NguoiDung $user, string $role): string
    {
        $base = $role === 'teacher' ? self::SYSTEM_PROMPT_TEACHER : self::SYSTEM_PROMPT_STUDENT;
        $name = trim((string) $user->ho_ten) ?: 'User';
        $roleLabel = $role === 'teacher' ? 'giáo viên' : 'học viên';

        return $base . "\n\n[Người dùng đang chat]\n- Họ tên: {$name}\n- Vai trò: {$roleLabel}\n- ID: {$user->id}";
    }

    private function llmProvider(): string
    {
        return strtolower((string) config('services.chat_llm.provider', 'gemini'));
    }

    private function llmTextSource(): string
    {
        return $this->llmProvider() === 'ollama' ? 'ollama' : 'gemini';
    }

    private function llmToolSource(): string
    {
        return $this->llmProvider() === 'ollama' ? 'ollama_tool' : 'gemini_tool';
    }

    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    private function buildTools(NguoiDung $user, string $role): array
    {
        if ($role === 'teacher') {
            $defs = array_merge(
                $this->teacher->definitions(),
                $this->premiumGuide->definitions($user),
            );
            if ($this->premiumGuard->hasActivePremium($user)) {
                $defs = array_merge($defs, $this->premium->definitions());
            }

            return $defs;
        }

        $defs = array_merge(
            $this->studentDb->definitions(),
            $this->studentMsg->definitions(),
            $this->studentCatalog->definitions(),
            $this->studentRel->definitions(),
            $this->premiumGuide->definitions($user),
        );

        if ($this->premiumGuard->hasActivePremium($user)) {
            $defs = array_merge($defs, array_filter(
                $this->premium->definitions(),
                static fn (array $def): bool => ($def['name'] ?? '') === 'student_get_monthly_report',
            ));
        }

        return $defs;
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    private function dispatchTool(NguoiDung $user, string $role, string $name, array $args): array
    {
        return match (true) {
            $name === 'student_get_my_teachers' || $name === 'student_get_teacher_lesson_count' => $this->studentRel->execute($user, $name, $args),
            str_starts_with($name, 'student_send_message')
                || str_starts_with($name, 'student_get_teacher_messages')
                || $name === 'student_get_assigned_teachers' => $this->studentMsg->execute($user, $name, $args),
            str_starts_with($name, 'student_get_lesson_categories')
                || str_starts_with($name, 'student_get_learning_inventory')
                || str_starts_with($name, 'student_get_next_lesson')
                || str_starts_with($name, 'student_get_vocabulary_mastery')
                || str_starts_with($name, 'student_get_teacher_suggestions') => $this->studentCatalog->execute($user, $name, $args),
            $name === 'student_get_premium_purchase_guide' || $name === 'teacher_get_premium_purchase_guide' => $this->premiumGuide->execute($user, $name),
            $name === 'student_get_monthly_report' || $name === 'teacher_get_class_report' => $this->premium->execute($user, $name, $args),
            str_starts_with($name, 'teacher_') => $this->teacher->execute($user, $name, $args),
            str_starts_with($name, 'student_') => $this->studentDb->execute($user, $name, $args),
            default => ['ok' => false, 'message' => 'Tool không hỗ trợ.'],
        };
    }

    /**
     * @param  list<array{role:string, content:string}>  $history
     * @return list<array<string, mixed>>
     */
    private function buildGeminiContents(array $history, string $userMessage): array
    {
        $contents = [];
        foreach ($history as $turn) {
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

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => trim($userMessage)]],
        ];

        return $contents;
    }

    /**
     * @param  list<array{name:string,description:string,args:array<string,string>}>  $toolDefs
     * @return list<array<string, mixed>>
     */
    private function toGeminiFunctionDeclarations(array $toolDefs): array
    {
        $declarations = [];
        foreach ($toolDefs as $def) {
            $properties = [];
            foreach ((array) ($def['args'] ?? []) as $argName => $argDesc) {
                $desc = (string) $argDesc;
                $lower = strtolower($desc);
                $type = 'STRING';
                if (str_contains($lower, 'integer') || str_contains($lower, 'int')) {
                    $type = 'INTEGER';
                } elseif (str_contains($lower, 'number') || str_contains($lower, 'float')) {
                    $type = 'NUMBER';
                } elseif (str_contains($lower, 'boolean') || str_contains($lower, 'bool')) {
                    $type = 'BOOLEAN';
                }
                $properties[(string) $argName] = [
                    'type' => $type,
                    'description' => $desc,
                ];
            }

            $declaration = [
                'name' => (string) ($def['name'] ?? ''),
                'description' => (string) ($def['description'] ?? ''),
            ];
            if ($properties !== []) {
                $declaration['parameters'] = [
                    'type' => 'OBJECT',
                    'properties' => $properties,
                ];
            }
            $declarations[] = $declaration;
        }

        return $declarations;
    }

    /**
     * When Gemini is unavailable, answer from DB using lightweight intent matching.
     *
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>,action_url?:string,action_label?:string}|null
     */
    private function tryDatabaseFallback(NguoiDung $user, string $role, string $message): ?array
    {
        $normalized = $this->normalizer->normalize($message);
        $rawLower = mb_strtolower(trim($message), 'UTF-8');
        $english = $this->prefersEnglish($message);

        if ($this->isSelfIdentityQuestion($normalized, $rawLower)) {
            return $this->trySelfIdentityFallback($user, $role, $english);
        }

        if ($this->isPremiumPurchaseQuestion($normalized, $rawLower)) {
            return $this->tryPremiumPurchaseGuideFallback($user, $english);
        }

        if ($role === 'student') {
            $teacherLessonFallback = $this->tryTeacherLessonCountFallback($user, $message, $english);
            if ($teacherLessonFallback !== null) {
                return $teacherLessonFallback;
            }

            if ($this->isTeacherListQuestion($normalized, $rawLower)) {
                $result = $this->studentRel->execute($user, 'student_get_my_teachers', []);
                if (($result['ok'] ?? false) === true) {
                    return [
                        'message' => $this->formatMyTeachersAnswer((array) ($result['data'] ?? []), $english),
                        'source' => 'db_fallback',
                        'tools_used' => [['name' => 'student_get_my_teachers', 'args' => []]],
                    ];
                }
            }

            if ($this->isScoreQuestion($normalized, $rawLower)) {
                $result = $this->studentDb->execute($user, 'student_get_personal_dashboard_data', ['days' => 7]);
                if (($result['ok'] ?? false) === true) {
                    return [
                        'message' => $this->formatDashboardAnswer((array) ($result['data'] ?? []), $english),
                        'source' => 'db_fallback',
                        'tools_used' => [['name' => 'student_get_personal_dashboard_data', 'args' => ['days' => 7]]],
                    ];
                }
            }

            if ($this->isPronunciationErrorQuestion($normalized, $rawLower)) {
                $result = $this->studentDb->execute($user, 'student_get_detailed_pronunciation_errors', ['days' => 30]);
                if (($result['ok'] ?? false) === true) {
                    return [
                        'message' => $this->formatPronunciationErrorsAnswer((array) ($result['data'] ?? []), $english),
                        'source' => 'db_fallback',
                        'tools_used' => [['name' => 'student_get_detailed_pronunciation_errors', 'args' => ['days' => 30]]],
                    ];
                }
            }

            if ($this->isNextLessonQuestion($normalized, $rawLower)) {
                $result = $this->studentCatalog->execute($user, 'student_get_next_lesson_recommendation', []);
                if (($result['ok'] ?? false) === true) {
                    return [
                        'message' => $this->formatNextLessonAnswer((array) ($result['data'] ?? []), $english),
                        'source' => 'db_fallback',
                        'tools_used' => [['name' => 'student_get_next_lesson_recommendation', 'args' => []]],
                        'action_url' => '/bai-hoc',
                        'action_label' => $english ? 'here' : 'tại đây',
                    ];
                }
            }

            if ($this->isLessonCategoryQuestion($normalized, $rawLower)) {
                $result = $this->studentCatalog->execute($user, 'student_get_lesson_categories', []);
                if (($result['ok'] ?? false) === true) {
                    return [
                        'message' => $this->formatLessonCategoriesAnswer((array) ($result['data'] ?? []), $english),
                        'source' => 'db_fallback',
                        'tools_used' => [['name' => 'student_get_lesson_categories', 'args' => []]],
                        'action_url' => '/bai-hoc',
                        'action_label' => $english ? 'here' : 'tại đây',
                    ];
                }
            }

            if ($this->isStudentLearningInventoryQuestion($normalized, $rawLower)) {
                $inventoryFallback = $this->tryLearningInventoryFallback($user, $english);
                if ($inventoryFallback !== null) {
                    return array_merge($inventoryFallback, [
                        'action_url' => '/bai-hoc',
                        'action_label' => $english ? 'here' : 'tại đây',
                    ]);
                }
            }

            if ($this->isStudentLearningPathQuestion($normalized, $rawLower)) {
                $pathFallback = $this->tryLearningPathFallback($user, $english);
                if ($pathFallback !== null) {
                    return array_merge($pathFallback, [
                        'action_url' => '/lo-trinh',
                        'action_label' => $english ? 'here' : 'tại đây',
                    ]);
                }
            }
        }

        if ($role === 'teacher' && $this->isTeacherStudentListQuestion($normalized, $rawLower)) {
            return $this->tryTeacherStudentListFallback($user, $english);
        }

        if ($role === 'teacher' && $this->isClassSizeQuestion($normalized, $rawLower)) {
            $result = $this->teacher->execute($user, 'teacher_get_class_overview', []);
            if (($result['ok'] ?? false) === true) {
                return [
                    'message' => $this->formatClassOverviewAnswer((array) ($result['data'] ?? []), $english),
                    'source' => 'db_fallback',
                    'tools_used' => [['name' => 'teacher_get_class_overview', 'args' => []]],
                ];
            }
        }

        if ($role === 'teacher' && $this->isTeacherOwnLessonCountQuestion($normalized, $rawLower)) {
            return $this->tryTeacherOwnLessonCountFallback($user, $english);
        }

        return null;
    }

    private function prefersEnglish(string $message): bool
    {
        if (preg_match('/\b(the|what|how many|which|who|is|are|my|teacher|lesson|category)\b/i', $message) === 1) {
            return true;
        }

        $vietnamese = preg_match_all('/[àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđ]/ui', $message) ?: 0;
        if ($vietnamese >= 2) {
            return false;
        }

        $latin = preg_match_all('/[a-zA-Z]/', $message) ?: 0;

        return $latin >= 12;
    }

    private function isTeacherListQuestion(string $normalized, string $rawLower): bool
    {
        if ($this->isTeacherLessonCountQuestion($normalized, $rawLower)) {
            return false;
        }

        if (preg_match('/\b(which|who)\b.*\b(teacher|teachers|instructor|tutor)\b/i', $rawLower) === 1) {
            return true;
        }
        if (preg_match('/\b(my|studying|study)\b.*\b(teacher|teachers)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'hoc voi ai')
            || str_contains($normalized, 'ai day em')
            || str_contains($normalized, 'danh sach giao vien')
            || str_contains($normalized, 'giao vien nao')
            || (
                (str_contains($normalized, 'giao vien') || str_contains($normalized, 'thay') || str_contains($normalized, 'co'))
                && (str_contains($normalized, 'ai') || str_contains($normalized, 'nao') || str_contains($normalized, 'dang hoc'))
                && ! str_contains($normalized, 'bai hoc')
            );
    }

    private function isScoreQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(score|grade|point)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'diem')
            || str_contains($normalized, 'tong diem')
            || str_contains($normalized, 'ket qua');
    }

    private function isPronunciationErrorQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(pronunciation|pronounce)\b.*\b(error|mistake)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'loi phat am')
            || str_contains($normalized, 'hay mac loi')
            || str_contains($normalized, 'doc sai');
    }

    private function isNextLessonQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(next|upcoming)\b.*\b(lesson|class)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'bai ke tiep')
            || str_contains($normalized, 'bai tiep theo')
            || str_contains($normalized, 'nen hoc');
    }

    private function isTeacherLessonCountQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(how many)\b.*\b(lesson|lessons)\b/i', $rawLower) === 1
            && preg_match('/\b(teacher|teach)\b/i', $rawLower) === 1) {
            return true;
        }

        return (str_contains($normalized, 'bao nhieu') || str_contains($normalized, 'co may'))
            && str_contains($normalized, 'bai hoc')
            && (str_contains($normalized, 'giao vien') || str_contains($normalized, 'thay') || str_contains($normalized, 'co'));
    }

    private function isStudentLearningPathQuestion(string $normalized, string $rawLower): bool
    {
        if (! str_contains($normalized, 'lo trinh')) {
            return false;
        }

        if (preg_match('/\b(my|current)\b.*\b(learning path|path)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'dang theo')
            || str_contains($normalized, 'dang hoc')
            || str_contains($normalized, 'cua toi')
            || str_contains($normalized, 'cua em')
            || str_contains($normalized, 'hien tai');
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>}|null
     */
    private function tryLearningPathFallback(NguoiDung $user, bool $english): ?array
    {
        $result = $this->studentDb->execute($user, 'student_get_learning_path_progress', []);
        if (($result['ok'] ?? false) !== true) {
            return null;
        }

        return [
            'message' => $this->formatLearningPathAnswer((array) ($result['data'] ?? []), $english),
            'source' => 'db_fallback',
            'tools_used' => [['name' => 'student_get_learning_path_progress', 'args' => []]],
        ];
    }

    private function isStudentLearningInventoryQuestion(string $normalized, string $rawLower): bool
    {
        if ($this->isTeacherLessonCountQuestion($normalized, $rawLower)) {
            return false;
        }

        if ($this->isStudentLearningPathQuestion($normalized, $rawLower)) {
            return false;
        }

        if ($this->isLessonCategoryQuestion($normalized, $rawLower)) {
            return false;
        }

        if (preg_match('/\b(how many)\b.*\b(lesson|lessons)\b.*\b(completed|finished|done)\b/i', $rawLower) === 1) {
            return true;
        }
        if (preg_match('/\b(how many)\b.*\b(lesson|lessons)\b.*\b(i|my|have)\b/i', $rawLower) === 1
            && preg_match('/\b(teacher|teach)\b/i', $rawLower) !== 1) {
            return true;
        }

        return (str_contains($normalized, 'da hoc') || str_contains($normalized, 'hoan thanh') || str_contains($normalized, 'tien do'))
            && (str_contains($normalized, 'bao nhieu') || str_contains($normalized, 'co may') || str_contains($normalized, 'may bai'))
            && str_contains($normalized, 'bai hoc');
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>}|null
     */
    private function tryLearningInventoryFallback(NguoiDung $user, bool $english): ?array
    {
        $result = $this->studentCatalog->execute($user, 'student_get_learning_inventory', []);
        if (($result['ok'] ?? false) !== true) {
            return null;
        }

        return [
            'message' => $this->formatLearningInventoryAnswer((array) ($result['data'] ?? []), $english),
            'source' => 'db_fallback',
            'tools_used' => [['name' => 'student_get_learning_inventory', 'args' => []]],
        ];
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>}|null
     */
    private function tryTeacherLessonCountFallback(NguoiDung $user, string $message, bool $english): ?array
    {
        if (! $this->isTeacherLessonCountQuestion($this->normalizer->normalize($message), mb_strtolower($message, 'UTF-8'))) {
            return null;
        }

        $myTeachers = $this->studentRel->execute($user, 'student_get_my_teachers', []);
        $teachers = (array) (($myTeachers['data'] ?? [])['teachers'] ?? []);
        $normalizedMessage = $this->normalizer->normalize($message);

        foreach ($teachers as $teacher) {
            $hoTen = (string) ($teacher['ho_ten'] ?? '');
            if ($hoTen === '' || ! $this->messageMentionsTeacherName($normalizedMessage, $hoTen)) {
                continue;
            }

            $result = $this->studentRel->execute($user, 'student_get_teacher_lesson_count', [
                'teacher_name' => $hoTen,
            ]);

            if (($result['ok'] ?? false) !== true) {
                continue;
            }

            return [
                'message' => $this->formatTeacherLessonCountAnswer((array) ($result['data'] ?? []), $english),
                'source' => 'db_fallback',
                'tools_used' => [['name' => 'student_get_teacher_lesson_count', 'args' => ['teacher_name' => $hoTen]]],
            ];
        }

        if (preg_match('/(?:cô|co|thầy|thay|giáo viên|giao vien)\s+(.+?)(?:\s+có|\s+co|\?|$)/ui', $message, $m) === 1) {
            $guess = trim($m[1]);
            $result = $this->studentRel->execute($user, 'student_get_teacher_lesson_count', [
                'teacher_name' => $guess,
            ]);
            if (($result['ok'] ?? false) === true) {
                return [
                    'message' => $this->formatTeacherLessonCountAnswer((array) ($result['data'] ?? []), $english),
                    'source' => 'db_fallback',
                    'tools_used' => [['name' => 'student_get_teacher_lesson_count', 'args' => ['teacher_name' => $guess]]],
                ];
            }
        }

        return null;
    }

    private function messageMentionsTeacherName(string $normalizedMessage, string $hoTen): bool
    {
        $nameNorm = $this->normalizer->normalize($hoTen);
        if ($nameNorm !== '' && str_contains($normalizedMessage, $nameNorm)) {
            return true;
        }

        $parts = array_values(array_filter(
            explode(' ', $nameNorm),
            static fn (string $p): bool => mb_strlen($p, 'UTF-8') >= 2,
        ));
        if (count($parts) < 2) {
            return false;
        }

        $tail = array_slice($parts, -2);

        foreach ($tail as $part) {
            if (! str_contains($normalizedMessage, $part)) {
                return false;
            }
        }

        return true;
    }

    private function isLessonCategoryQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(how many)\b.*\b(categor|topic|subject)\b/i', $rawLower) === 1) {
            return true;
        }

        return (str_contains($normalized, 'bao nhieu') || str_contains($normalized, 'co may'))
            && (str_contains($normalized, 'danh muc') || str_contains($normalized, 'chu de'))
            || str_contains($normalized, 'danh muc bai hoc')
            || str_contains($normalized, 'so danh muc')
            || str_contains($normalized, 'liet ke danh muc');
    }

    private function isClassSizeQuestion(string $normalized, string $rawLower): bool
    {
        if ($this->isTeacherStudentListQuestion($normalized, $rawLower)) {
            return false;
        }

        if (preg_match('/\b(how many)\b.*\b(student|students)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'bao nhieu hoc vien')
            || str_contains($normalized, 'so hoc vien')
            || (str_contains($normalized, 'lop em co') && ! str_contains($normalized, 'danh sach'));
    }

    private function isTeacherStudentListQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(list|who are)\b.*\b(my )?(students?|class)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'danh sach hoc vien')
            || str_contains($normalized, 'liet ke hoc vien')
            || (
                str_contains($normalized, 'hoc vien')
                && (
                    str_contains($normalized, 'danh sach')
                    || str_contains($normalized, 'liet ke')
                    || str_contains($normalized, 'gom')
                    || (str_contains($normalized, 'ten') && ! str_contains($normalized, 'bao nhieu'))
                )
                && (
                    str_contains($normalized, 'dang hoc')
                    || str_contains($normalized, 'theo toi')
                    || str_contains($normalized, 'cua toi')
                    || str_contains($normalized, 'hoc voi toi')
                )
            );
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>}|null
     */
    private function tryTeacherStudentListFallback(NguoiDung $user, bool $english): ?array
    {
        $result = $this->teacher->execute($user, 'teacher_list_my_students', []);
        if (($result['ok'] ?? false) !== true) {
            return null;
        }

        return [
            'message' => $this->formatTeacherStudentListAnswer((array) ($result['data'] ?? []), $english),
            'source' => 'db_fallback',
            'tools_used' => [['name' => 'teacher_list_my_students', 'args' => []]],
        ];
    }

    private function isPremiumPurchaseQuestion(string $normalized, string $rawLower): bool
    {
        if (preg_match('/\b(buy|purchase|upgrade)\b.*\bpremium\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'premium')
            && (
                str_contains($normalized, 'mua')
                || str_contains($normalized, 'nang cap')
                || str_contains($normalized, 'dang ky')
                || str_contains($normalized, 'huong dan')
                || str_contains($normalized, 'cach mua')
                || str_contains($normalized, 'goi premium')
            );
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>,action_url?:string}|null
     */
    private function tryPremiumPurchaseGuideFallback(NguoiDung $user, bool $english): ?array
    {
        $toolName = ((int) $user->vai_tro_id) === NguoiDung::ROLE_TEACHER
            ? 'teacher_get_premium_purchase_guide'
            : 'student_get_premium_purchase_guide';
        $result = $this->premiumGuide->execute($user, $toolName);
        if (($result['ok'] ?? false) !== true) {
            return null;
        }

        $data = (array) ($result['data'] ?? []);

        return [
            'message' => $this->formatPremiumPurchaseGuideAnswer($data, $english),
            'source' => 'db_fallback',
            'tools_used' => [['name' => $toolName, 'args' => []]],
            'action_url' => (string) ($data['profile_path'] ?? '/profile'),
        ];
    }

    private function formatPremiumPurchaseGuideAnswer(array $data, bool $english): string
    {
        $isTeacher = ($data['role'] ?? '') === 'teacher';
        $profilePath = (string) ($data['profile_path'] ?? '/profile');

        if (($data['has_active_premium'] ?? false) === true) {
            $sub = (array) ($data['active_subscription'] ?? []);
            $name = (string) ($sub['ten_goi'] ?? 'Premium');
            $expiry = (string) ($sub['ngay_het_han'] ?? '');

            if ($english) {
                return "You already have an active {$name} package" . ($expiry !== '' ? " (expires {$expiry})" : '') . '.';
            }

            return $isTeacher
                ? "Thầy cô đang dùng gói {$name}" . ($expiry !== '' ? " (hết hạn {$expiry})" : '') . '. Khi hết hạn, mua lại tại Hồ sơ → Gói Premium.'
                : "Con đang dùng gói {$name}" . ($expiry !== '' ? " (hết hạn {$expiry})" : '') . '. Khi hết hạn, ba mẹ có thể mua lại tại Hồ sơ → Gói Premium.';
        }

        /** @var array<string, mixed>|null $package */
        $package = isset($data['package']) && is_array($data['package']) ? $data['package'] : null;
        if ($package === null) {
            return $english
                ? 'Premium is not available for your account type right now.'
                : ($isTeacher
                    ? 'Hiện chưa có gói Premium mở bán cho tài khoản giáo viên.'
                    : 'Hiện chưa có gói Premium mở bán cho tài khoản học viên.');
        }

        $tenGoi = (string) ($package['ten_goi'] ?? 'Premium');
        $gia = $this->formatVnd((int) ($package['gia'] ?? 0));
        $days = (int) ($package['thoi_han_ngay'] ?? 30);
        $balance = $this->formatVnd((int) ($data['wallet_balance'] ?? 0));
        $sufficient = ($data['sufficient_balance'] ?? false) === true;
        /** @var list<string> $features */
        $features = (array) ($package['tinh_nang'] ?? []);
        $featureText = $features !== [] ? implode(', ', $features) : '';

        if ($english) {
            $message = "Package \"{$tenGoi}\": {$gia} for {$days} days. Wallet balance: {$balance}.";
            if ($featureText !== '') {
                $message .= " Includes: {$featureText}.";
            }
            $message .= ' Go to Profile → Wallet (top up if needed) → Premium tab → Buy with wallet balance.';

            return $message;
        }

        if ($isTeacher) {
            $message = "Gói {$tenGoi}: {$gia} / {$days} ngày";
            if ($featureText !== '') {
                $message .= " ({$featureText})";
            }
            $message .= ". Số dư ví: {$balance}.";
            if ($sufficient) {
                $message .= ' Thầy cô vào Hồ sơ → Ví & Giao dịch (nếu cần) → tab Gói Premium → bấm «Mua bằng số dư ví».';
            } else {
                $message .= ' Thầy cô nạp thêm tiền vào ví (Hồ sơ → Ví & Giao dịch), sau đó vào tab Gói Premium → «Mua bằng số dư ví».';
            }

            return $message;
        }

        $message = "Gói {$tenGoi}: {$gia} / {$days} ngày";
        if ($featureText !== '') {
            $message .= " — gồm {$featureText}";
        }
        $message .= ". Số dư ví hiện tại: {$balance}.";
        if ($sufficient) {
            $message .= ' Con nhờ ba mẹ vào Hồ sơ → tab Ví & Giao dịch (nếu cần) → Gói Premium → «Mua bằng số dư ví».';
        } else {
            $message .= ' Con nhờ ba mẹ nạp tiền vào ví (Hồ sơ → Ví & Giao dịch, chuyển khoản theo hướng dẫn), rồi mua tại tab Gói Premium.';
        }

        return $message;
    }

    private function formatVnd(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }

    private function isSelfIdentityQuestion(string $normalized, string $rawLower): bool
    {
        if (str_contains($normalized, 'giao vien')
            || str_contains($normalized, 'thay')
            || str_contains($normalized, 'tro ly')
            || str_contains($normalized, 'echokids')
            || str_contains($normalized, 'chatbox')) {
            return false;
        }

        if (preg_match('/\b(who am i|what is my name|what\'s my name)\b/i', $rawLower) === 1) {
            return true;
        }

        return str_contains($normalized, 'toi la ai')
            || str_contains($normalized, 'em la ai')
            || str_contains($normalized, 'thong tin cua toi')
            || str_contains($normalized, 'thong tin ca nhan')
            || (
                str_contains($normalized, 'ten')
                && str_contains($normalized, 'la gi')
                && (str_contains($normalized, 'toi') || str_contains($normalized, 'em') || str_contains($normalized, 'cua toi'))
            );
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>}|null
     */
    private function trySelfIdentityFallback(NguoiDung $user, string $role, bool $english): ?array
    {
        $name = trim((string) $user->ho_ten);
        if ($name === '') {
            return null;
        }

        return [
            'message' => $this->formatSelfIdentityAnswer($user, $role, $name, $english),
            'source' => 'db_fallback',
            'tools_used' => [['name' => 'session_user_profile', 'args' => []]],
        ];
    }

    private function formatSelfIdentityAnswer(NguoiDung $user, string $role, string $name, bool $english): string
    {
        if ($role === 'teacher') {
            $stats = $this->teacher->execute($user, 'teacher_get_my_lesson_stats', []);
            $totalLessons = (int) (($stats['data'] ?? [])['total_lessons'] ?? 0);

            if ($english) {
                $message = "You are {$name}, a teacher on EchoKids.";
                if ($totalLessons > 0) {
                    $message .= " You have created {$totalLessons} lesson(s) on the platform.";
                }

                return $message;
            }

            $message = "Thầy cô là {$name}, giáo viên trên EchoKids.";
            if ($totalLessons > 0) {
                $message .= " Thầy cô đã tạo {$totalLessons} bài học trên hệ thống.";
            }

            return $message;
        }

        $inventory = $this->studentCatalog->execute($user, 'student_get_learning_inventory', []);
        $completed = (int) (($inventory['data'] ?? [])['completed_count'] ?? 0);
        $teachers = $this->studentRel->execute($user, 'student_get_my_teachers', []);
        /** @var list<array{ho_ten?:string}> $teacherRows */
        $teacherRows = (array) (($teachers['data'] ?? [])['teachers'] ?? []);
        $teacherNames = array_values(array_filter(array_map(
            static fn (array $row): string => trim((string) ($row['ho_ten'] ?? '')),
            $teacherRows,
        )));

        if ($english) {
            $message = "You are {$name}, a student on EchoKids.";
            if ($completed > 0) {
                $message .= " You have completed {$completed} lesson(s).";
            }
            if ($teacherNames !== []) {
                $message .= ' Your teacher(s): ' . implode(', ', $teacherNames) . '.';
            }

            return $message;
        }

        $message = "Con là {$name}, học viên trên EchoKids.";
        if ($completed > 0) {
            $message .= " Con đã hoàn thành {$completed} bài học.";
        }
        if (count($teacherNames) === 1) {
            $message .= " Con đang được cô {$teacherNames[0]} hướng dẫn.";
        } elseif (count($teacherNames) > 1) {
            $message .= ' Con đang được ' . implode(', ', $teacherNames) . ' hướng dẫn.';
        }

        return $message;
    }

    private function isTeacherOwnLessonCountQuestion(string $normalized, string $rawLower): bool
    {
        if (str_contains($normalized, 'hoc vien') || str_contains($normalized, 'hoc sinh')) {
            return false;
        }

        if (preg_match('/\b(how many)\b.*\b(lesson|lessons)\b.*\b(created|create|i made|my)\b/i', $rawLower) === 1) {
            return true;
        }

        return (str_contains($normalized, 'tao') || str_contains($normalized, 'da tao'))
            && str_contains($normalized, 'bai hoc')
            && (
                str_contains($normalized, 'bao nhieu')
                || str_contains($normalized, 'co may')
                || str_contains($normalized, 'so luong')
                || str_contains($normalized, 'may bai')
            );
    }

    /**
     * @return array{message:string,source:string,tools_used:list<array{name:string,args:array<string,mixed>}>}|null
     */
    private function tryTeacherOwnLessonCountFallback(NguoiDung $user, bool $english): ?array
    {
        $result = $this->teacher->execute($user, 'teacher_get_my_lesson_stats', []);
        if (($result['ok'] ?? false) !== true) {
            return null;
        }

        return [
            'message' => $this->formatTeacherOwnLessonCountAnswer((array) ($result['data'] ?? []), $english),
            'source' => 'db_fallback',
            'tools_used' => [['name' => 'teacher_get_my_lesson_stats', 'args' => []]],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatMyTeachersAnswer(array $data, bool $english): string
    {
        $count = (int) ($data['count'] ?? 0);
        /** @var list<array{ho_ten?:string}> $teachers */
        $teachers = (array) ($data['teachers'] ?? []);
        $names = array_values(array_filter(array_map(
            static fn (array $t): string => trim((string) ($t['ho_ten'] ?? '')),
            $teachers,
        )));

        if ($count === 0 || $names === []) {
            return $english
                ? 'You are not connected with any teachers in the system yet.'
                : 'Hiện tại con chưa được giáo viên nào kèm cặp trong hệ thống.';
        }

        if ($english) {
            if ($count === 1) {
                return "You are currently guided by teacher {$names[0]}.";
            }

            return 'You are currently guided by ' . $count . ' teachers: ' . implode(', ', $names) . '.';
        }

        if ($count === 1) {
            return "Hiện tại con đang được cô {$names[0]} hướng dẫn.";
        }

        $last = array_pop($names);
        $prefix = implode(', ', $names);

        return "Hiện tại con đang được {$count} giáo viên hướng dẫn: {$prefix} và {$last}.";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatDashboardAnswer(array $data, bool $english): string
    {
        $practice = (array) ($data['practice'] ?? []);
        $sessions = (int) ($practice['total_sessions'] ?? 0);
        $avg = (float) ($practice['avg_score'] ?? 0);

        if ($english) {
            return "In the last 7 days you completed {$sessions} practice session(s), with an average score of {$avg}.";
        }

        return "Trong 7 ngày qua, con đã luyện {$sessions} phiên, điểm trung bình {$avg}.";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatPronunciationErrorsAnswer(array $data, bool $english): string
    {
        $most = (string) ($data['most_common_error'] ?? '');
        $total = (int) ($data['total_errors'] ?? 0);

        if ($english) {
            return $total === 0
                ? 'No pronunciation errors recorded in the last 30 days.'
                : "You made {$total} pronunciation mistake(s) recently; the most common type is {$most}.";
        }

        return $total === 0
            ? '30 ngày qua con chưa ghi nhận lỗi phát âm nào.'
            : "30 ngày qua con có {$total} lỗi phát âm, hay gặp nhất là {$most}.";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatTeacherLessonCountAnswer(array $data, bool $english): string
    {
        if (($data['ambiguous'] ?? false) === true) {
            /** @var list<array{ho_ten?:string, active_lessons?:int, total_lessons?:int}> $matches */
            $matches = (array) ($data['matches'] ?? []);
            $lines = array_map(static function (array $row): string {
                $name = (string) ($row['ho_ten'] ?? '');
                $active = (int) ($row['active_lessons'] ?? 0);
                $total = (int) ($row['total_lessons'] ?? 0);

                return "{$name}: {$active} bài đang mở / {$total} bài tổng";
            }, $matches);

            return $english
                ? 'Multiple teachers matched: ' . implode('; ', $lines)
                : 'Có nhiều giáo viên trùng tên: ' . implode('; ', $lines) . '.';
        }

        $name = (string) ($data['ho_ten'] ?? '');
        $active = (int) ($data['active_lessons'] ?? 0);
        $total = (int) ($data['total_lessons'] ?? 0);
        $pending = (int) ($data['pending_lessons'] ?? 0);

        if ($english) {
            return "Teacher {$name} has {$active} active lesson(s) ({$total} total created, {$pending} pending approval).";
        }

        if ($pending > 0) {
            return "Cô {$name} có {$active} bài học đang mở cho học viên (tổng {$total} bài đã tạo, {$pending} bài đang chờ duyệt).";
        }

        return "Cô {$name} có {$active} bài học đang mở (tổng cộng {$total} bài cô đã tạo trên hệ thống).";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatLessonCategoriesAnswer(array $data, bool $english): string
    {
        $count = (int) ($data['category_count'] ?? 0);
        $totalLessons = (int) ($data['total_active_lessons'] ?? 0);
        /** @var list<array{ten_danh_muc?:string, lesson_count?:int}> $categories */
        $categories = (array) ($data['categories'] ?? []);

        if ($count === 0) {
            return $english
                ? 'There are no lesson categories published on the system yet.'
                : 'Hiện chưa có danh mục bài học nào đang hiển thị trên hệ thống.';
        }

        $names = array_values(array_filter(array_map(
            static fn (array $row): string => trim((string) ($row['ten_danh_muc'] ?? '')),
            $categories,
        )));

        if ($english) {
            $list = implode(', ', array_slice($names, 0, 8));
            $suffix = count($names) > 8 ? '…' : '';

            return "There are {$count} lesson categories with {$totalLessons} active lesson(s): {$list}{$suffix}.";
        }

        if ($count <= 6) {
            $list = implode(', ', $names);

            return "Hệ thống đang có {$count} danh mục bài học (tổng {$totalLessons} bài đang mở): {$list}.";
        }

        $preview = implode(', ', array_slice($names, 0, 5));

        return "Hệ thống đang có {$count} danh mục bài học (tổng {$totalLessons} bài đang mở), gồm: {$preview}… và các danh mục khác.";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatLearningPathAnswer(array $data, bool $english): string
    {
        /** @var array<string, mixed> $primary */
        $primary = (array) ($data['primary_path'] ?? []);
        if (($primary['has_path'] ?? false) !== true) {
            return $english
                ? 'You do not have a learning path yet. Open the Learning Path page to get started.'
                : 'Con chưa có lộ trình học. Vào mục Lộ trình để xem thêm nhé.';
        }

        $name = trim((string) ($primary['ten_lo_trinh'] ?? ''));
        $pct = (int) ($primary['progress_percentage'] ?? 0);
        $next = trim((string) ($primary['next_milestone'] ?? ''));

        if ($english) {
            $message = $name !== ''
                ? "You are following \"{$name}\" ({$pct}% complete)."
                : "Your learning path is {$pct}% complete.";
            if ($next !== '' && $next !== 'Hoàn thành lộ trình') {
                $message .= " Next lesson: \"{$next}\".";
            }

            return $message;
        }

        $message = $name !== ''
            ? "Con đang theo học \"{$name}\" và đã hoàn thành {$pct}%."
            : "Lộ trình của con đã hoàn thành {$pct}%.";
        if ($next !== '' && $next !== 'Hoàn thành lộ trình') {
            $message .= " Bài tiếp theo là \"{$next}\".";
        }

        return $message;
    }

    private function formatLearningInventoryAnswer(array $data, bool $english): string
    {
        $completed = (int) ($data['completed_count'] ?? 0);
        $inProgress = (int) ($data['in_progress_count'] ?? 0);
        /** @var list<array{title?:string, status?:string}> $lessons */
        $lessons = (array) ($data['lessons'] ?? []);
        $recentTitles = array_values(array_filter(array_map(
            static fn (array $row): string => trim((string) ($row['title'] ?? '')),
            array_slice(
                array_values(array_filter($lessons, static fn (array $row): bool => ($row['status'] ?? '') === 'completed')),
                0,
                3,
            ),
        )));

        if ($english) {
            if ($completed === 0 && $inProgress === 0) {
                return 'You have not completed any lessons yet. Open the Lessons page to start your first one.';
            }
            $base = "You have completed {$completed} lesson(s)";
            if ($inProgress > 0) {
                $base .= " and have {$inProgress} in progress";
            }
            if ($recentTitles !== []) {
                $base .= ', including: ' . implode(', ', $recentTitles);
            }

            return $base . '.';
        }

        if ($completed === 0 && $inProgress === 0) {
            return 'Con chưa hoàn thành bài học nào. Vào mục Bài học để bắt đầu học nhé.';
        }

        $message = "Con đã hoàn thành {$completed} bài học";
        if ($inProgress > 0) {
            $message .= ", đang học dở {$inProgress} bài";
        }
        if ($recentTitles !== []) {
            $message .= ', gồm: ' . implode(', ', $recentTitles);
        }

        return $message . '.';
    }

    private function formatNextLessonAnswer(array $data, bool $english): string
    {
        $title = trim((string) ($data['recommended_lesson_title'] ?? $data['next_lesson_title'] ?? $data['title'] ?? ''));
        if ($title === '') {
            return $english
                ? 'No next lesson recommendation is available yet. Open the Lessons page to browse.'
                : 'Chưa có bài gợi ý. Con vào mục Bài học để xem danh sách nhé.';
        }

        return $english
            ? "Your recommended next lesson is \"{$title}\"."
            : "Bài tiếp theo cô gợi ý cho con là \"{$title}\".";
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatClassOverviewAnswer(array $data, bool $english): string
    {
        $count = (int) ($data['student_count'] ?? 0);
        $avg = (float) ($data['avg_score_7d'] ?? 0);

        if ($english) {
            return "Your class has {$count} student(s); average score in the last 7 days is {$avg}.";
        }

        return "Lớp của thầy cô có {$count} học viên; điểm trung bình 7 ngày qua là {$avg}.";
    }

    private function formatTeacherStudentListAnswer(array $data, bool $english): string
    {
        $count = (int) ($data['count'] ?? 0);
        /** @var list<array{ho_ten?:string}> $students */
        $students = (array) ($data['students'] ?? []);
        $names = array_values(array_filter(array_map(
            static fn (array $row): string => trim((string) ($row['ho_ten'] ?? '')),
            $students,
        )));

        if ($count === 0 || $names === []) {
            return $english
                ? 'You have no students connected yet.'
                : 'Hiện chưa có học viên nào đang kết nối với thầy cô.';
        }

        if ($english) {
            return 'Your class has ' . $count . ' student(s): ' . implode(', ', $names) . '.';
        }

        if ($count === 1) {
            return "Học viên đang theo học thầy cô là {$names[0]}.";
        }

        if ($count <= 10) {
            $last = array_pop($names);
            $prefix = implode(', ', $names);

            return "Có {$count} học viên đang theo học thầy cô: {$prefix} và {$last}.";
        }

        $preview = implode(', ', array_slice($names, 0, 8));

        return "Có {$count} học viên đang theo học thầy cô, gồm: {$preview}… và các bạn khác.";
    }

    private function formatTeacherOwnLessonCountAnswer(array $data, bool $english): string
    {
        $total = (int) ($data['total_lessons'] ?? 0);
        $active = (int) ($data['active_lessons'] ?? 0);
        $pending = (int) ($data['pending_lessons'] ?? 0);
        $rejected = (int) ($data['rejected_lessons'] ?? 0);

        if ($english) {
            if ($total === 0) {
                return 'You have not created any lessons yet.';
            }

            $message = "You have created {$total} lesson(s): {$active} active";
            if ($pending > 0) {
                $message .= ", {$pending} pending approval";
            }
            if ($rejected > 0) {
                $message .= ", {$rejected} rejected";
            }

            return $message . '.';
        }

        if ($total === 0) {
            return 'Thầy cô chưa tạo bài học nào trên hệ thống.';
        }

        $message = "Thầy cô đã tạo {$total} bài học, trong đó {$active} bài đang mở cho học viên";
        if ($pending > 0) {
            $message .= ", {$pending} bài đang chờ duyệt";
        }
        if ($rejected > 0) {
            $message .= ", {$rejected} bài bị từ chối";
        }

        return $message . '.';
    }
}
