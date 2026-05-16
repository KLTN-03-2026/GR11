<?php

namespace App\Http\Controllers;

use App\Events\StudentSentMessage;
use App\Models\BaiHoc;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\QuanHeGvHv;
use App\Support\ChatDisplayTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StudentChatController extends Controller
{
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = ChatMessage::query()
            ->join('chat_sessions as cs', 'cs.id', '=', 'chat_messages.session_id')
            ->where('cs.user_id', $user->id)
            ->whereNotNull('cs.lesson_id')
            ->where('chat_messages.role', 'teacher')
            ->where('chat_messages.is_read_by_student', false)
            ->count();

        return response()->json([
            'unread_count' => (int) $count,
        ]);
    }

    public function getSessions(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (Schema::hasColumn('chat_messages', 'is_delivered_to_student')) {
            ChatMessage::query()
                ->join('chat_sessions as cs', 'cs.id', '=', 'chat_messages.session_id')
                ->where('cs.user_id', $user->id)
                ->whereNotNull('cs.lesson_id')
                ->where('chat_messages.role', 'teacher')
                ->where('chat_messages.is_delivered_to_student', false)
                ->update(['chat_messages.is_delivered_to_student' => true]);
        }

        $sessions = ChatSession::query()
            ->with([
                'lesson' => fn ($q) => $q->select('id', 'tieu_de', 'nguoi_tao_id'),
                'lesson.nguoiTao' => fn ($q) => $q->select('id', 'ho_ten', 'anh_dai_dien'),
                'messages' => fn ($q) => $q->orderByDesc('created_at'),
            ])
            ->withCount(['messages as unread_count' => fn ($q) => $q
                ->where('role', 'teacher')
                ->where('is_read_by_student', false)])
            ->where('user_id', $user->id)
            ->whereNotNull('lesson_id')
            ->orderByDesc('updated_at')
            ->get();

        $teacherChats = [];
        foreach ($sessions as $session) {
            $teacher = $session->lesson?->nguoiTao;
            if (! $teacher) {
                continue;
            }
            $chatMessages = $session->messages
                ->filter(static fn (ChatMessage $msg): bool => in_array($msg->role, ['user', 'teacher'], true));
            if ($chatMessages->isEmpty()) {
                continue;
            }
            $latest = $chatMessages->first();
            $teacherId = (int) $teacher->id;
            if (! isset($teacherChats[$teacherId]) || $latest->created_at > $teacherChats[$teacherId]['_ts']) {
                $teacherChats[$teacherId] = [
                    'teacher_id' => $teacherId,
                    'teacher_name' => (string) $teacher->ho_ten,
                    'teacher_avatar' => $teacher->anh_dai_dien ?: null,
                    'session_id' => (int) $session->id,
                    'lesson_id' => (int) $session->lesson_id,
                    'lesson_title' => (string) ($session->lesson?->tieu_de ?? ''),
                    'last_message' => (string) $latest->content,
                    'last_message_time' => optional($latest->created_at)->toIso8601String(),
                    'unread_count' => (int) ($session->unread_count ?? 0),
                    '_ts' => $latest->created_at,
                ];
            } else {
                $teacherChats[$teacherId]['unread_count'] += (int) ($session->unread_count ?? 0);
            }
        }

        $data = collect($teacherChats)
            ->sortByDesc('_ts')
            ->map(static function (array $row): array {
                unset($row['_ts']);

                return $row;
            })
            ->values()
            ->all();

        return response()->json($data);
    }

    public function createSession(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'lesson_id' => 'required|integer',
            'teacher_id' => 'nullable|integer',
        ]);

        $lesson = BaiHoc::query()->find($data['lesson_id']);
        if (! $lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }

        $teacherId = (int) ($data['teacher_id'] ?? 0);
        if ($teacherId <= 0) {
            $teacherId = (int) $lesson->nguoi_tao_id;
        }

        if ($teacherId > 0) {
            $coQuanHe = QuanHeGvHv::query()
                ->where('hoc_vien_id', $user->id)
                ->where('giao_vien_id', $teacherId)
                ->where('trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
                ->exists();

            if (! $coQuanHe) {
                return response()->json([
                    'error' => 'Bạn chưa đăng ký với giáo viên này. Vui lòng vào trang bài học và bấm đăng ký với giáo viên trước.',
                ], 403);
            }
        }

        $session = ChatSession::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $data['lesson_id'],
        ], [
            'status' => 'active',
        ]);

        return response()->json([
            'session_id' => $session->id,
            'teacher_id' => $teacherId,
        ]);
    }

    public function sendMessage(Request $request, $sessionId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $session = ChatSession::find($sessionId);
        if (! $session || $session->user_id !== $user->id) {
            return response()->json(['error' => 'Session not found or access denied'], 404);
        }

        $payload = [
            'session_id' => $session->id,
            'role' => 'user',
            'content' => $data['message'],
            'is_read_by_teacher' => false,
            'created_at' => now(),
        ];
        if (Schema::hasColumn('chat_messages', 'is_delivered_to_teacher')) {
            $payload['is_delivered_to_teacher'] = false;
        }

        $message = ChatMessage::create($payload);

        // touch session so teacher list sees updated_at
        $session->touch();

        // Dispatch event to notify teacher(s)
        event(new StudentSentMessage($session, $message));

        return response()->json([
            'id' => $message->id,
            'sender' => 'Học viên',
            'text' => $message->content,
            'time' => ChatDisplayTime::format($message->created_at),
            'status' => 'sent',
        ]);
    }

    public function getMessages(Request $request, $sessionId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $session = ChatSession::with('lesson')->find($sessionId);
        if (! $session || $session->user_id !== $user->id) {
            return response()->json(['error' => 'Session not found or access denied'], 404);
        }

        $updatePayload = [];
        if (Schema::hasColumn('chat_messages', 'is_delivered_to_student')) {
            $updatePayload['is_delivered_to_student'] = true;
        }
        if (Schema::hasColumn('chat_messages', 'is_read_by_student')) {
            $updatePayload['is_read_by_student'] = true;
        }
        if (! empty($updatePayload)) {
            $teacherMessageQuery = ChatMessage::where('session_id', $session->id)
                ->where('role', 'teacher');

            if (Schema::hasColumn('chat_messages', 'is_delivered_to_student') && Schema::hasColumn('chat_messages', 'is_read_by_student')) {
                $teacherMessageQuery->where(function ($q) {
                    $q->where('is_delivered_to_student', false)
                        ->orWhere('is_read_by_student', false);
                });
            } elseif (Schema::hasColumn('chat_messages', 'is_delivered_to_student')) {
                $teacherMessageQuery->where('is_delivered_to_student', false);
            } elseif (Schema::hasColumn('chat_messages', 'is_read_by_student')) {
                $teacherMessageQuery->where('is_read_by_student', false);
            }

            $teacherMessageQuery->update($updatePayload);
        }

        $messages = ChatMessage::where('session_id', $session->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($session) {
                $parsed = $this->parseSuggestionMessage((string) $msg->content, (int) ($session->lesson_id ?? 0));
                return [
                    'id' => $msg->id,
                    'sender' => $msg->role === 'teacher' ? 'Giáo viên' : 'Học viên',
                    'text' => $parsed['text'],
                    'action_url' => $parsed['url'],
                    'action_label' => $parsed['label'],
                    'time' => ChatDisplayTime::format($msg->created_at),
                    'status' => $msg->role === 'user'
                        ? ($msg->is_read_by_teacher ? 'seen' : (($msg->is_delivered_to_teacher ?? false) ? 'delivered' : 'sent'))
                        : (($msg->is_read_by_student ?? false) ? 'seen' : (($msg->is_delivered_to_student ?? false) ? 'delivered' : 'sent')),
                ];
            });

        return response()->json([
            'session' => [
                'id' => $session->id,
                'lesson' => $session->lesson->tieu_de ?? '',
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * @return array{url:?string,label:?string}
     */
    private function parseSuggestionMessage(string $content, int $fallbackLessonId): array
    {
        $cleanText = trim($content);
        $lessonId = $fallbackLessonId;
        if (preg_match('/\[\[lesson_id:(\d+)\]\]/', $content, $matches) === 1) {
            $lessonId = (int) $matches[1];
            $cleanText = trim(str_replace($matches[0], '', $content));
        }

        $isSuggestion = str_starts_with($cleanText, 'Giáo viên gợi ý bài:');
        if (! $isSuggestion || $lessonId <= 0) {
            return ['text' => $cleanText, 'url' => null, 'label' => null];
        }

        return ['text' => $cleanText, 'url' => '/chi-tiet-bai-hoc/'.$lessonId, 'label' => null];
    }
}
