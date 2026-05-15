<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\NguoiDung;
use App\Services\AI\Rag\Analytics\LearningAnalyticsRepository;

class TeacherClassTools
{
    public function __construct(
        private readonly LearningAnalyticsRepository $analytics,
    ) {}

    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'teacher_get_class_overview',
                'description' => 'Số lượng học viên, điểm TB 7 ngày, tin nhắn chưa đọc (không có danh sách tên).',
                'args' => [],
            ],
            [
                'name' => 'teacher_list_my_students',
                'description' => 'Danh sách tên học viên đang kết nối/học với thầy cô. Dùng khi hỏi "danh sách học viên", "học viên đang học tôi gồm".',
                'args' => [],
            ],
            [
                'name' => 'teacher_get_my_lesson_stats',
                'description' => 'Đếm số bài học thầy/cô đã tạo (tổng, đang mở, chờ duyệt, bị từ chối). Dùng khi hỏi "tôi đã tạo bao nhiêu bài học", số bài của mình.',
                'args' => [],
            ],
            [
                'name' => 'teacher_get_student_learning_summary',
                'description' => 'Learning summary for one student by id or name',
                'args' => [
                    'hoc_vien_id' => 'integer, optional student id',
                    'name_search' => 'string, optional partial name',
                ],
            ],
            [
                'name' => 'teacher_get_path_assignments',
                'description' => 'Learning paths assigned to students',
                'args' => [],
            ],
            [
                'name' => 'teacher_list_unread_chats',
                'description' => 'Students with unread chat messages',
                'args' => [
                    'limit' => 'integer, max students (default 10)',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    public function execute(NguoiDung $teacher, string $toolName, array $args): array
    {
        return match ($toolName) {
            'teacher_get_class_overview' => [
                'ok' => true,
                'data' => $this->analytics->classOverview((int) $teacher->id),
            ],
            'teacher_list_my_students' => [
                'ok' => true,
                'data' => $this->analytics->studentsForTeacher((int) $teacher->id),
            ],
            'teacher_get_my_lesson_stats' => [
                'ok' => true,
                'data' => $this->analytics->lessonStatsForTeacher((int) $teacher->id),
            ],
            'teacher_get_student_learning_summary' => $this->studentSummary($teacher, $args),
            'teacher_get_path_assignments' => [
                'ok' => true,
                'data' => [
                    'paths' => $this->analytics->pathAssignmentsForTeacher((int) $teacher->id),
                ],
            ],
            'teacher_list_unread_chats' => [
                'ok' => true,
                'data' => [
                    'students' => $this->analytics->unreadChatsForTeacher(
                        (int) $teacher->id,
                        min(20, max(1, (int) ($args['limit'] ?? 10)))
                    ),
                ],
            ],
            default => [
                'ok' => false,
                'message' => 'Công cụ không được hỗ trợ.',
            ],
        };
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    private function studentSummary(NguoiDung $teacher, array $args): array
    {
        $hocVienId = isset($args['hoc_vien_id']) ? (int) $args['hoc_vien_id'] : null;
        $nameSearch = isset($args['name_search']) ? (string) $args['name_search'] : null;

        $summary = $this->analytics->studentLearningSummary(
            (int) $teacher->id,
            $hocVienId ?: null,
            $nameSearch
        );

        if ($summary === null) {
            return [
                'ok' => false,
                'message' => 'Không tìm thấy học viên trong lớp của thầy cô.',
            ];
        }

        return ['ok' => true, 'data' => $summary];
    }
}
