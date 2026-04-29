<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatSystemRequest;
use App\Models\NguoiDung;
use App\Services\AI\Rag\Pipelines\ChatRagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatBoxAIController extends Controller
{
    public function __construct(
        private readonly ChatRagService $chatRagService
    ) {
    }

    private function ensureStudentOnly(?NguoiDung $user): ?JsonResponse
    {
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if ((int) $user->vai_tro_id !== NguoiDung::ROLE_USER) {
            return response()->json([
                'status' => false,
                'message' => 'Chatbox AI chỉ dành cho học viên.',
            ], 403);
        }

        return null;
    }

    public function chatSystem(ChatSystemRequest $request): JsonResponse
    {
        $user = $request->user();
        $guardError = $this->ensureStudentOnly($user);
        if ($guardError !== null) {
            return $guardError;
        }

        $result = $this->chatRagService->reply(
            $user,
            (string) $request->input('message'),
            $request->integer('session_id') ?: null,
            (string) $request->input('input_type', 'text')
        );

        return response()->json([
            'status' => true,
            'session_id' => $result['session_id'] ?? null,
            'message' => $result['message'],
            'action_url' => $result['action_url'] ?? null,
            'action_label' => $result['action_label'] ?? null,
            'meta' => $result['meta'] ?? [],
        ]);
    }

    public function session(Request $request): JsonResponse
    {
        $user = $request->user();
        $guardError = $this->ensureStudentOnly($user);
        if ($guardError !== null) {
            return $guardError;
        }

        $result = $this->chatRagService->resolveSessionMeta(
            $user,
            $request->integer('session_id') ?: null
        );

        return response()->json([
            'status' => true,
            'session_id' => $result['session_id'],
            'session_status' => $result['status'],
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $guardError = $this->ensureStudentOnly($user);
        if ($guardError !== null) {
            return $guardError;
        }

        $sessionId = $request->integer('session_id');
        if ($sessionId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Thiếu session_id.',
            ], 422);
        }

        $rows = $this->chatRagService->latestHistoryBySession($user, $sessionId, 20);
        $messages = collect($rows)->map(static fn(array $row): array => [
            'role' => ($row['role'] ?? 'user') === 'model' ? 'ai' : 'user',
            'text' => (string) ($row['content'] ?? ''),
        ])->values();

        return response()->json([
            'status' => true,
            'session_id' => $sessionId,
            'data' => $messages,
        ]);
    }
}
