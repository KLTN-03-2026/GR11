<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatSystemRequest;
use App\Models\NguoiDung;
use App\Services\AI\Rag\Pipelines\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatBoxAIController extends Controller
{
    public function __construct(
        private readonly ChatService $chat,
    ) {}

    private function ensureChatAiUser(?NguoiDung $user): ?JsonResponse
    {
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $roleId = (int) $user->vai_tro_id;
        if (!in_array($roleId, [NguoiDung::ROLE_USER, NguoiDung::ROLE_TEACHER], true)) {
            return response()->json([
                'status'  => false,
                'message' => 'Chatbox AI chỉ dành cho học viên và giáo viên.',
            ], 403);
        }

        return null;
    }

    public function greeting(Request $request): JsonResponse
    {
        $user = $request->user();
        $guardError = $this->ensureChatAiUser($user);
        if ($guardError !== null) {
            return $guardError;
        }

        $result = $this->chat->greet($user);

        return response()->json([
            'status'      => true,
            'message'     => $result['message'],
            'suggestions' => $result['suggestions'],
            'case'        => $result['case'],
            'digest'      => $result['context']['digest'] ?? $result['digest'] ?? null,
        ]);
    }

    public function chatSystem(ChatSystemRequest $request): JsonResponse
    {
        $user = $request->user();
        $guardError = $this->ensureChatAiUser($user);
        if ($guardError !== null) {
            return $guardError;
        }

        $result = $this->chat->reply(
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
            'report_snapshot' => $result['report_snapshot'] ?? null,
            'meta' => $result['meta'] ?? [],
        ]);
    }

    public function session(Request $request): JsonResponse
    {
        $user = $request->user();
        $guardError = $this->ensureChatAiUser($user);
        if ($guardError !== null) {
            return $guardError;
        }

        $result = $this->chat->resolveSessionMeta(
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
        $guardError = $this->ensureChatAiUser($user);
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

        $rows = $this->chat->latestHistoryBySession($user, $sessionId, 20);
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
