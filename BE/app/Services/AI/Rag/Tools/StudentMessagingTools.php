<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\NguoiDung;
use App\Models\QuanHeGvHv;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Real-time messaging tools for student-teacher-admin communication.
 * Enables direct messaging without AI intermediary for real-time collaboration.
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
                    'type' => 'string, message type (text|request), default text',
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
     * Send message to teacher
     *
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    private function sendMessageToTeacher(NguoiDung $student, array $args): array
    {
        $teacherId = (int) ($args['teacher_id'] ?? 0);
        $message = (string) ($args['message'] ?? '');
        $type = (string) ($args['type'] ?? 'text');

        if (!$teacherId || !$message) {
            return [
                'ok' => false,
                'message' => 'Thiếu thông tin giáo viên hoặc nội dung tin nhắn',
            ];
        }

        $teacher = NguoiDung::find($teacherId);
        if (!$teacher || (int) $teacher->vai_tro_id !== NguoiDung::ROLE_TEACHER) {
            return [
                'ok' => false,
                'message' => 'Giáo viên không tồn tại',
            ];
        }

        // Verify student is assigned to this teacher
        $relationship = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->where('hoc_vien_id', $student->id)
            ->exists();

        if (!$relationship) {
            return [
                'ok' => false,
                'message' => 'Bạn không được phân công với giáo viên này',
            ];
        }

        // Create or get session between student and teacher
        $session = ChatSession::firstOrCreate(
            [
                'type' => 'direct_message',
                'participant_1_id' => min($student->id, $teacherId),
                'participant_2_id' => max($student->id, $teacherId),
            ],
            [
                'trang_thai' => 'active',
                'ngay_tao' => now(),
            ]
        );

        // Save message
        ChatMessage::create([
            'session_id' => $session->id,
            'sender_id' => $student->id,
            'noi_dung' => $message,
            'type' => $type,
            'role' => 'user',
            'ngay_tao' => now(),
        ]);

        return [
            'ok' => true,
            'message' => 'Tin nhắn đã được gửi',
            'data' => [
                'session_id' => $session->id,
                'recipient_id' => $teacherId,
            ],
        ];
    }

    /**
     * Get messages from teachers
     *
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
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'cm.sender_id')
            ->where('cs.type', 'direct_message')
            ->where(function ($q) use ($student) {
                $q->where('cs.participant_1_id', $student->id)
                    ->orWhere('cs.participant_2_id', $student->id);
            })
            ->where('cm.sender_id', '!=', $student->id)
            ->where('cm.ngay_tao', '>=', $from)
            ->orderByDesc('cm.ngay_tao')
            ->limit($limit)
            ->select('cm.id', 'cm.sender_id', 'cm.noi_dung', 'cm.ngay_tao', 'nd.ho_ten')
            ->get();

        return [
            'ok' => true,
            'data' => [
                'message_count' => $messages->count(),
                'messages' => $messages->map(fn ($m) => [
                    'id' => $m->id,
                    'from' => $m->ho_ten,
                    'content' => $m->noi_dung,
                    'time' => $m->ngay_tao,
                ])->toArray(),
            ],
        ];
    }

    /**
     * Get assigned teachers
     *
     * @return array<string,mixed>
     */
    private function getAssignedTeachers(NguoiDung $student): array
    {
        $teachers = DB::table('quan_he_gv_hv as qg')
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'qg.giao_vien_id')
            ->where('qg.hoc_vien_id', $student->id)
            ->select('nd.id', 'nd.ho_ten', 'nd.email')
            ->distinct()
            ->get();

        return [
            'ok' => true,
            'data' => [
                'teacher_count' => $teachers->count(),
                'teachers' => $teachers->map(fn ($t) => [
                    'id' => $t->id,
                    'name' => $t->ho_ten,
                    'email' => $t->email,
                ])->toArray(),
            ],
        ];
    }
}
