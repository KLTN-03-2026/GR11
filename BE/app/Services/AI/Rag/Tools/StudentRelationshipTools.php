<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\BaiHoc;
use App\Models\NguoiDung;
use App\Models\QuanHeGvHv;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentRelationshipTools
{
    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'student_get_my_teachers',
                'description' => 'Lấy danh sách giáo viên đang kèm học viên. CHỈ dùng khi hỏi em học với ai / có những giáo viên nào. KHÔNG dùng khi hỏi một giáo viên cụ thể có bao nhiêu bài học.',
                'args' => [],
            ],
            [
                'name' => 'student_get_teacher_lesson_count',
                'description' => 'Đếm số bài học do MỘT giáo viên cụ thể tạo (theo tên hoặc một phần tên: Thu Hà, Tuấn, Lê Minh Tuấn). BẮT BUỘC dùng khi câu hỏi có "bao nhiêu bài học" kèm tên thầy/cô.',
                'args' => [
                    'teacher_name' => 'string, tên giáo viên hoặc một phần tên',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    public function execute(NguoiDung $student, string $toolName, array $args): array
    {
        return match ($toolName) {
            'student_get_my_teachers' => $this->getMyTeachers($student),
            'student_get_teacher_lesson_count' => $this->getTeacherLessonCount($student, $args),
            default => [
                'ok' => false,
                'message' => 'Công cụ không được hỗ trợ.',
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function getMyTeachers(NguoiDung $student): array
    {
        $teachers = DB::table('quan_he_gv_hvs as qh')
            ->join('nguoi_dungs as gv', 'gv.id', '=', 'qh.giao_vien_id')
            ->where('qh.hoc_vien_id', $student->id)
            ->where('qh.trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
            ->orderByDesc('qh.ngay_ket_noi')
            ->get(['gv.id', 'gv.ho_ten', 'gv.email', 'qh.ngay_ket_noi']);

        $items = $teachers->map(static fn ($row): array => [
            'id' => (int) $row->id,
            'ho_ten' => (string) ($row->ho_ten ?? ''),
            'email' => (string) ($row->email ?? ''),
            'ngay_ket_noi' => $row->ngay_ket_noi,
        ])->values()->all();

        return [
            'ok' => true,
            'data' => [
                'count' => count($items),
                'teachers' => $items,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    private function getTeacherLessonCount(NguoiDung $student, array $args): array
    {
        $teacherName = trim((string) ($args['teacher_name'] ?? ''));
        if ($teacherName === '') {
            return [
                'ok' => false,
                'message' => 'Thiếu tên giáo viên.',
            ];
        }

        $teacherName = preg_replace('/^(cô|co|thầy|thay|giáo viên|giao vien)\s+/ui', '', $teacherName) ?? $teacherName;
        $teacherName = trim($teacherName);

        $assigned = $this->findAssignedTeachers($student, $teacherName);
        if ($assigned->isEmpty()) {
            return [
                'ok' => false,
                'message' => 'Không tìm thấy giáo viên "' . $teacherName . '" trong danh sách giáo viên đang kèm con.',
            ];
        }

        if ($assigned->count() > 1) {
            $rows = $assigned->map(function ($row): array {
                $stats = $this->countLessonsByTeacher((int) $row->id);

                return [
                    'teacher_id' => (int) $row->id,
                    'ho_ten' => (string) ($row->ho_ten ?? ''),
                    ...$stats,
                ];
            })->values()->all();

            return [
                'ok' => true,
                'data' => [
                    'ambiguous' => true,
                    'search' => $teacherName,
                    'matches' => $rows,
                ],
            ];
        }

        $teacher = $assigned->first();
        $stats = $this->countLessonsByTeacher((int) $teacher->id);

        return [
            'ok' => true,
            'data' => array_merge([
                'ambiguous' => false,
                'teacher_id' => (int) $teacher->id,
                'ho_ten' => (string) ($teacher->ho_ten ?? ''),
            ], $stats),
        ];
    }

    /**
     * @return Collection<int, object{id:int, ho_ten:?string}>
     */
    private function findAssignedTeachers(NguoiDung $student, string $teacherName): Collection
    {
        $base = DB::table('quan_he_gv_hvs as qh')
            ->join('nguoi_dungs as gv', 'gv.id', '=', 'qh.giao_vien_id')
            ->where('qh.hoc_vien_id', $student->id)
            ->where('qh.trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
            ->where('gv.vai_tro_id', NguoiDung::ROLE_TEACHER);

        $exact = (clone $base)
            ->where('gv.ho_ten', 'like', '%' . $teacherName . '%')
            ->get(['gv.id', 'gv.ho_ten']);

        if ($exact->isNotEmpty()) {
            return $exact;
        }

        $parts = array_values(array_filter(
            preg_split('/\s+/u', $teacherName) ?: [],
            static fn (string $p): bool => mb_strlen($p, 'UTF-8') >= 2,
        ));

        if ($parts === []) {
            return collect();
        }

        $query = clone $base;
        foreach ($parts as $part) {
            $query->where('gv.ho_ten', 'like', '%' . $part . '%');
        }

        return $query->get(['gv.id', 'gv.ho_ten']);
    }

    /**
     * @return array{total_lessons:int, active_lessons:int, pending_lessons:int, rejected_lessons:int}
     */
    private function countLessonsByTeacher(int $teacherId): array
    {
        $row = DB::table('bai_hocs')
            ->where('nguoi_tao_id', $teacherId)
            ->selectRaw('COUNT(*) as total_lessons')
            ->selectRaw('SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as active_lessons', [BaiHoc::TRANG_THAI_HOAT_DONG])
            ->selectRaw('SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as pending_lessons', [BaiHoc::TRANG_THAI_CHO_DUYET])
            ->selectRaw('SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as rejected_lessons', [BaiHoc::TRANG_THAI_TU_CHOI])
            ->first();

        return [
            'total_lessons' => (int) ($row->total_lessons ?? 0),
            'active_lessons' => (int) ($row->active_lessons ?? 0),
            'pending_lessons' => (int) ($row->pending_lessons ?? 0),
            'rejected_lessons' => (int) ($row->rejected_lessons ?? 0),
        ];
    }
}
