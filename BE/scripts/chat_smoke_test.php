<?php

declare(strict_types=1);

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Scripts\FailingSmokeGeminiClient;
use Scripts\SmokeGeminiClient;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/SmokeGeminiClient.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$useMock = in_array('--live', $argv ?? [], true) === false;
if ($useMock) {
    echo "Mode: mock Gemini (pass --live to use real API)\n\n";
} else {
    echo "Mode: live Gemini API\n\n";
}

$cases = [
    [
        'label' => 'Student who am I',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Tôi là ai?',
        'expected_tool' => 'session_user_profile',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Student teachers',
        'email' => 'phamlananh@gmail.com',
        'message' => 'em đang học với giáo viên nào?',
        'expected_tool' => 'student_get_my_teachers',
    ],
    [
        'label' => 'Student teachers (English, DB fallback)',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Which teacher am I studying with?',
        'expected_tool' => 'student_get_my_teachers',
        'force_fallback' => true,
    ],
    [
        'label' => 'Student weekly score',
        'email' => 'phamlananh@gmail.com',
        'message' => 'điểm tuần qua của em?',
        'expected_tool' => 'student_get_personal_dashboard_data',
    ],
    [
        'label' => 'Student next lesson',
        'email' => 'phamlananh@gmail.com',
        'message' => 'bài kế tiếp em nên học là gì?',
        'expected_tool' => 'student_get_next_lesson_recommendation',
    ],
    [
        'label' => 'Student lesson categories',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Có bao nhiêu danh mục bài học?',
        'expected_tool' => 'student_get_lesson_categories',
    ],
    [
        'label' => 'Student completed lessons',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Tôi đã học bao nhiêu bài học?',
        'expected_tool' => 'student_get_learning_inventory',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Student learning path',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Lộ trình mà tôi đang theo học?',
        'expected_tool' => 'student_get_learning_path_progress',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Student teacher lesson count',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Cô giáo Thu Hà có bao nhiêu bài học?',
        'expected_tool' => 'student_get_teacher_lesson_count',
    ],
    [
        'label' => 'Student teacher Tuấn lesson count',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Thầy Tuấn có bao nhiêu bài học?',
        'expected_tool' => 'student_get_teacher_lesson_count',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Student pronunciation errors',
        'email' => 'phamlananh@gmail.com',
        'message' => 'lỗi phát âm em hay mắc?',
        'expected_tool' => 'student_get_detailed_pronunciation_errors',
    ],
    [
        'label' => 'Teacher class overview',
        'email' => 'tranthithuha@gmail.com',
        'message' => 'lớp em có bao nhiêu học viên?',
        'expected_tool' => 'teacher_get_class_overview',
    ],
    [
        'label' => 'Teacher student list',
        'email' => 'tranthithuha@gmail.com',
        'message' => 'Danh sách học viên đang học tôi gồm:',
        'expected_tool' => 'teacher_list_my_students',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Teacher own lesson count',
        'email' => 'tranthithuha@gmail.com',
        'message' => 'Tôi đã tạo ra bao nhiêu bài học?',
        'expected_tool' => 'teacher_get_my_lesson_stats',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Student premium purchase guide',
        'email' => 'phamlananh@gmail.com',
        'message' => 'Hướng dẫn tôi mua gói premium',
        'expected_tool' => 'student_get_premium_purchase_guide',
        'expect_source' => 'db_direct',
    ],
    [
        'label' => 'Teacher premium purchase guide',
        'email' => 'tranthithuha@gmail.com',
        'message' => 'Hướng dẫn mua gói premium',
        'expected_tool' => 'teacher_get_premium_purchase_guide',
        'expect_source' => 'db_direct',
    ],
];

$passed = 0;
$failed = 0;

foreach ($cases as $case) {
    $user = NguoiDung::query()->where('email', $case['email'])->first();
    if ($user === null) {
        echo "[FAIL] {$case['label']}: user not found ({$case['email']})\n";
        $failed++;
        continue;
    }

    $user->tokens()->where('name', 'chat-smoke')->delete();
    $token = $user->createToken('chat-smoke')->plainTextToken;

    $app->forgetInstance(App\Services\AI\Rag\LLM\LlmClientInterface::class);
    $app->forgetInstance(App\Services\AI\Rag\Pipelines\ChatService::class);
    $app->forgetInstance(App\Http\Controllers\ChatBoxAIController::class);
    $app->bind(App\Services\AI\Rag\LLM\LlmClientInterface::class, static function () use ($case, $useMock): App\Services\AI\Rag\LLM\LlmClientInterface {
        if (! empty($case['force_fallback'])) {
            return new FailingSmokeGeminiClient();
        }
        if ($useMock) {
            return new SmokeGeminiClient();
        }

        $provider = strtolower((string) config('services.chat_llm.provider', 'gemini'));

        return $provider === 'ollama'
            ? new App\Services\AI\Rag\LLM\OllamaClient()
            : new App\Services\AI\Rag\LLM\GeminiClient();
    });

    $request = Request::create('/api/chat/system', 'POST', [
        'message' => $case['message'],
    ]);
    $request->headers->set('Authorization', 'Bearer ' . $token);
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(static fn () => $user);

    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    if ($response->getStatusCode() !== 200) {
        echo "[FAIL] {$case['label']}: HTTP {$response->getStatusCode()} {$response->getContent()}\n";
        $failed++;
        continue;
    }

    /** @var array<string, mixed> $json */
    $json = json_decode($response->getContent(), true) ?? [];
    $toolsUsed = collect($json['meta']['tools_used'] ?? [])->pluck('name')->all();
    $expected = $case['expected_tool'];
    $source = (string) ($json['meta']['source'] ?? '');
    $ok = in_array($expected, $toolsUsed, true)
        || (
            ! empty($case['force_fallback'])
            && $source === 'db_fallback'
            && in_array($expected, $toolsUsed, true)
        )
        || (
            ! empty($case['expect_source'])
            && $source === $case['expect_source']
            && in_array($expected, $toolsUsed, true)
        );

    if ($ok) {
        echo "[PASS] {$case['label']}: tool={$expected}\n";
        echo '       answer: ' . mb_substr((string) ($json['message'] ?? ''), 0, 120) . "\n";
        $passed++;
    } else {
        echo "[FAIL] {$case['label']}: expected {$expected}, got [" . implode(', ', $toolsUsed) . "]\n";
        echo '       answer: ' . mb_substr((string) ($json['message'] ?? ''), 0, 120) . "\n";
        echo '       source: ' . ($json['meta']['source'] ?? 'n/a') . "\n";
        if (! empty($json['meta']['error_detail'])) {
            echo '       error: ' . $json['meta']['error_detail'] . "\n";
        }
        $failed++;
    }
}

echo "\nSummary: {$passed} passed, {$failed} failed\n";
exit($failed > 0 ? 1 : 0);
