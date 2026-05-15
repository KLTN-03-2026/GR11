<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\BaiHoc;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\NguoiDung;
use App\Models\QuanHeGvHv;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Student ↔ teacher messaging via lesson-scoped chat_sessions (same schema as ChatController).
 */
class StudentMessagingTools
{
    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'student_send_message_to_teacher',
                'description' => 'Send a direct message to assigned teacher for real-time communication',
                'args' => [
                    'teacher_id' => 'integer, ID of the teacher',
                    'message' => 'string, message content',
                    'lesson_id' => 'integer, optional lesson context for chat session',
                ],
            ],
            [
                'name' => 'student_get_teacher_messages',
                'description' => 'Get messages from assigned teachers',
                'args' => [
                    'limit' => 'integer, max messages to retrieve (default 20)',
                    'days' => 'integer, messages from last N days (default 7)',
                ],
            ],
            [
                'name' => 'student_get_assigned_teachers',
                'description' => 'Get list of assigned teachers for direct messaging',
                'args' => [],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    public function execute(NguoiDung $student, string $toolName, array $args): array
    {
        return match ($toolName) {
            'student_send_message_to_teacher' => $this->sendMessageToTeacher($student, $args),
            'student_get_teacher_messages' => $this->getTeacherMessages($student, $args),
            'student_get_assigned_teachers' => $this->getAssignedTeachers($student),
            default => [
                'ok' => false,
                'message' => 'Công cụ không được hỗ trợ.',
            ],
        };
    }

    /**
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    private function sendMessageToTeacher(NguoiDung $student, array $args): array
    {
        $teacherId = (int) ($args['teacher_id'] ?? 0);
        $message = trim((string) ($args['message'] ?? ''));
        $lessonId = isset($args['lesson_id']) ? (int) $args['lesson_id'] : 0;

        if (! $teacherId || $message === '') {
            return [
                'ok' => false,
                'message' => 'Thiếu thông tin giáo viên hoặc nội dung tin nhắn',
            ];
        }

        $teacher = NguoiDung::find($teacherId);
        if (! $teacher || (int) $teacher->vai_tro_id !== NguoiDung::ROLE_TEACHER) {
            return [
                'ok' => false,
                'message' => 'Giáo viên không tồn tại',
            ];
        }

        $relationship = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->where('hoc_vien_id', $student->id)
            ->exists();

        if (! $relationship) {
            return [
                'ok' => false,
                'message' => 'Bạn không được phân công với giáo viên này',
            ];
        }

        if ($lessonId <= 0) {
            $lessonId = (int) (ChatSession::query()
                ->where('user_id', $student->id)
                ->whereNotNull('lesson_id')
                ->orderByDesc('updated_at')
                ->value('lesson_id') ?? 0);
        }

        if ($lessonId <= 0) {
            $lessonId = (int) (BaiHoc::query()
                ->where('nguoi_tao_id', $teacherId)
                ->where('trang_thai', 'approved')
                ->orderBy('id')
                ->value('id') ?? 0);
        }

        if ($lessonId <= 0) {
            return [
                'ok' => false,
                'message' => 'Chưa có phiên chat bài học. Con mở chat giáo viên từ bài học trước nhé.',
            ];
        }

        $session = ChatSession::firstOrCreate(
            [
                'user_id' => $student->id,
                'lesson_id' => $lessonId,
            ],
            ['status' => 'active']
        );

        $payload = [
            'session_id' => $session->id,
            'role' => 'user',
            'content' => $message,
            'is_read_by_teacher' => false,
            'created_at' => now(),
        ];
        if (Schema::hasColumn('chat_messages', 'is_delivered_to_teacher')) {
            $payload['is_delivered_to_teacher'] = false;
        }

        ChatMessage::create($payload);
        $session->touch();

        return [
            'ok' => true,
            'message' => 'Tin nhắn đã được gửi',
            'data' => [
                'session_id' => $session->id,
                'recipient_id' => $teacherId,
                'lesson_id' => $lessonId,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    private function getTeacherMessages(NguoiDung $student, array $args): array
    {
        $limit = min(50, max(1, (int) ($args['limit'] ?? 20)));
        $days = max(1, (int) ($args['days'] ?? 7));
        $from = Carbon::now()->subDays($days);

        $messages = DB::table('chat_messages as cm')
            ->join('chat_sessions as cs', 'cs.id', '=', 'cm.session_id')
            ->where('cs.user_id', $student->id)
            ->whereNotNull('cs.lesson_id')
            ->where('cm.role', 'teacher')
            ->where('cm.created_at', '>=', $from)
            ->orderByDesc('cm.created_at')
            ->limit($limit)
            ->select('cm.id', 'cm.content', 'cm.created_at', 'cm.is_read_by_student')
            ->get();

        return [
            'ok' => true,
            'data' => [
                'message_count' => $messages->count(),
                'unread_count' => $messages->where('is_read_by_student', false)->count(),
                'messages' => $messages->map(static fn ($m): array => [
                    'id' => $m->id,
                    'content' => $m->content,
                    'time' => $m->created_at,
                    'read' => (bool) $m->is_read_by_student,
                ])->toArray(),
            ],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function getAssignedTeachers(NguoiDung $student): array
    {
        $teachers = DB::table('quan_he_gv_hvs as qg')
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'qg.giao_vien_id')
            ->where('qg.hoc_vien_id', $student->id)
            ->select('nd.id', 'nd.ho_ten', 'nd.email')
            ->distinct()
            ->get();

        return [
            'ok' => true,
            'data' => [
                'teacher_count' => $teachers->count(),
                'teachers' => $teachers->map(static fn ($t): array => [
                    'id' => $t->id,
                    'name' => $t->ho_ten,
                    'email' => $t->email,
                ])->toArray(),
            ],
        ];
    }
}
