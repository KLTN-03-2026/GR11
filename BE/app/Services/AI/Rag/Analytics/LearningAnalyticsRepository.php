<?php

namespace App\Services\AI\Rag\Analytics;

use App\Models\BaiHoc;
use App\Models\ChiTietLoTrinh;
use App\Models\GoiYLuyenTap;
use App\Models\LoTrinhCaNhan;
use App\Models\LoTrinhTraPhi;
use App\Models\NguoiDung;
use App\Models\QuyenLoTrinh;
use App\Models\PhienLuyenTap;
use App\Models\QuanHeGvHv;
use App\Models\TienDoBaiHoc;
use App\Models\TienDoHocTap;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LearningAnalyticsRepository
{
    public const LESSON_STATUS_IN_PROGRESS = 0;

    public const LESSON_STATUS_COMPLETED = 1;

    public const VOCAB_STATUS_LEARNING = 0;

    public const VOCAB_STATUS_MASTERED = 2;

    /**
     * @return array{
     *   completed_count:int,
     *   in_progress_count:int,
     *   lessons:list<array<string,mixed>>,
     *   categories:list<array<string,mixed>>
     * }
     */
    public function learningInventory(NguoiDung $student): array
    {
        $rows = TienDoBaiHoc::query()
            ->where('hoc_vien_id', $student->id)
            ->join('bai_hocs as bh', 'bh.id', '=', 'tien_do_bai_hocs.bai_hoc_id')
            ->leftJoin('danh_muc_bai_hocs as dm', 'dm.id', '=', 'bh.danh_muc_id')
            ->orderByDesc('tien_do_bai_hocs.thoi_gian_hoc_cuoi')
            ->get([
                'tien_do_bai_hocs.bai_hoc_id',
                'tien_do_bai_hocs.trang_thai',
                'tien_do_bai_hocs.phan_tram_hoan_thanh',
                'tien_do_bai_hocs.thoi_gian_hoc_cuoi',
                'bh.tieu_de',
                'bh.thu_tu',
                'dm.ten_danh_muc',
            ]);

        $completed = $rows->where('trang_thai', self::LESSON_STATUS_COMPLETED);
        $inProgress = $rows->where('trang_thai', '!=', self::LESSON_STATUS_COMPLETED);

        $categories = $rows
            ->groupBy('ten_danh_muc')
            ->map(static fn (Collection $group, $name): array => [
                'category' => $name ?: 'Khác',
                'lesson_count' => $group->count(),
            ])
            ->values()
            ->all();

        return [
            'completed_count' => $completed->count(),
            'in_progress_count' => $inProgress->count(),
            'lessons' => $rows->take(20)->map(static fn ($r): array => [
                'bai_hoc_id' => (int) $r->bai_hoc_id,
                'title' => (string) $r->tieu_de,
                'status' => (int) $r->trang_thai === self::LESSON_STATUS_COMPLETED ? 'completed' : 'in_progress',
                'percent' => round((float) ($r->phan_tram_hoan_thanh ?? 0), 1),
                'category' => (string) ($r->ten_danh_muc ?? ''),
                'last_studied' => $r->thoi_gian_hoc_cuoi
                    ? Carbon::parse((string) $r->thoi_gian_hoc_cuoi)->format('Y-m-d')
                    : null,
            ])->values()->all(),
            'categories' => $categories,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function nextLessonRecommendation(NguoiDung $student, ?int $afterBaiHocId = null): ?array
    {
        $anchorId = $afterBaiHocId;
        if ($anchorId === null) {
            $lastSession = PhienLuyenTap::query()
                ->where('nguoi_dung_id', $student->id)
                ->whereNotNull('bai_hoc_id')
                ->orderByDesc('id')
                ->first();
            $anchorId = $lastSession?->bai_hoc_id;
        }

        if ($anchorId !== null) {
            $anchor = BaiHoc::query()->find($anchorId);
            if ($anchor !== null) {
                $next = BaiHoc::query()
                    ->where('trang_thai', 'approved')
                    ->where('danh_muc_id', $anchor->danh_muc_id)
                    ->where('thu_tu', '>', $anchor->thu_tu)
                    ->orderBy('thu_tu')
                    ->first();
                if ($next !== null) {
                    return $this->lessonPayload($next, 'sequential_in_category');
                }
            }
        }

        $path = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', $student->id)
            ->orderByDesc('id')
            ->first();
        if ($path !== null) {
            $completedIds = TienDoBaiHoc::query()
                ->where('hoc_vien_id', $student->id)
                ->where('trang_thai', self::LESSON_STATUS_COMPLETED)
                ->pluck('bai_hoc_id')
                ->all();

            $nextDetail = ChiTietLoTrinh::query()
                ->where('lo_trinh_id', $path->id)
                ->whereNotIn('bai_hoc_id', $completedIds)
                ->orderBy('thu_tu_uu_tien')
                ->with('baiHoc:id,tieu_de,thu_tu,cap_do')
                ->first();

            if ($nextDetail?->baiHoc !== null) {
                return array_merge(
                    $this->lessonPayload($nextDetail->baiHoc, 'learning_path'),
                    ['path_name' => (string) $path->ten_lo_trinh]
                );
            }
        }

        $studiedIds = TienDoBaiHoc::query()
            ->where('hoc_vien_id', $student->id)
            ->where('trang_thai', self::LESSON_STATUS_COMPLETED)
            ->pluck('bai_hoc_id')
            ->all();

        $fallback = BaiHoc::query()
            ->where('trang_thai', 'approved')
            ->whereNotIn('id', $studiedIds)
            ->orderBy('thu_tu')
            ->first();

        return $fallback ? $this->lessonPayload($fallback, 'level_fallback') : null;
    }

    /**
     * @return array{
     *   mastered_count:int,
     *   learning_count:int,
     *   practiced_7d:int,
     *   practiced_30d:int
     * }
     */
    public function vocabularyMastery(NguoiDung $student, int $days = 7): array
    {
        $from = Carbon::now()->subDays($days);
        $from30 = Carbon::now()->subDays(30);

        $mastered = (int) TienDoHocTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('trang_thai', self::VOCAB_STATUS_MASTERED)
            ->count();

        $learning = (int) TienDoHocTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('trang_thai', '!=', self::VOCAB_STATUS_MASTERED)
            ->count();

        $practiced7d = (int) DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->where(function ($q) use ($from): void {
                $q->where('p.thoi_gian_bat_dau', '>=', $from)
                    ->orWhere('p.created_at', '>=', $from);
            })
            ->distinct('ct.tu_vung_id')
            ->count('ct.tu_vung_id');

        $practiced30d = (int) DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->where(function ($q) use ($from30): void {
                $q->where('p.thoi_gian_bat_dau', '>=', $from30)
                    ->orWhere('p.created_at', '>=', $from30);
            })
            ->distinct('ct.tu_vung_id')
            ->count('ct.tu_vung_id');

        return [
            'mastered_count' => $mastered,
            'learning_count' => $learning,
            'practiced_7d' => $practiced7d,
            'practiced_30d' => $practiced30d,
            'period_days' => $days,
        ];
    }

    /**
     * @return array{unread_count:int, total_count:int, suggestions:list<array<string,mixed>>}
     */
    public function teacherSuggestions(NguoiDung $student, int $limit = 10): array
    {
        $rows = GoiYLuyenTap::query()
            ->where('hoc_vien_id', $student->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'giao_vien_id', 'noi_dung', 'da_doc', 'created_at']);

        return [
            'unread_count' => $rows->where('da_doc', false)->count(),
            'total_count' => $rows->count(),
            'suggestions' => $rows->map(static fn (GoiYLuyenTap $r): array => [
                'id' => $r->id,
                'teacher_id' => (int) $r->giao_vien_id,
                'content' => (string) $r->noi_dung,
                'read' => (bool) $r->da_doc,
                'created_at' => optional($r->created_at)->format('Y-m-d H:i'),
            ])->values()->all(),
        ];
    }

    /**
     * @return array{
     *   path_count:int,
     *   accessible_count:int,
     *   paths:list<array<string,mixed>>
     * }
     */
    public function learningPathsSummary(NguoiDung $student): array
    {
        $rows = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', $student->id)
            ->with('traPhi')
            ->orderByDesc('id')
            ->get();

        $paths = $rows->map(function (LoTrinhCaNhan $path) use ($student): array {
            $paidPlan = $path->traPhi;
            $price = $paidPlan ? (int) $paidPlan->gia : 0;
            $approved = $paidPlan && (int) $paidPlan->trang_thai === LoTrinhTraPhi::TRANG_THAI_DA_DUYET;
            $isPaid = $price > 0 && $approved;
            $purchased = $isPaid && QuyenLoTrinh::query()
                ->where('lo_trinh_id', $path->id)
                ->where('hoc_vien_id', $student->id)
                ->exists();

            return [
                'lo_trinh_id' => (int) $path->id,
                'ten_lo_trinh' => (string) $path->ten_lo_trinh,
                'lesson_count' => ChiTietLoTrinh::query()
                    ->where('lo_trinh_id', $path->id)
                    ->count(),
                'can_study' => ! $isPaid || $purchased,
                'requires_purchase' => $isPaid && ! $purchased,
            ];
        })->values()->all();

        $accessibleCount = collect($paths)->where('can_study', true)->count();

        return [
            'path_count' => count($paths),
            'accessible_count' => $accessibleCount,
            'paths' => $paths,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function learningPathProgress(NguoiDung $student): array
    {
        $learningPath = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', $student->id)
            ->orderByDesc('id')
            ->first();

        if ($learningPath === null) {
            return ['has_path' => false];
        }

        $pathDetails = ChiTietLoTrinh::query()
            ->where('lo_trinh_id', $learningPath->id)
            ->with('baiHoc:id,tieu_de')
            ->orderBy('thu_tu_uu_tien')
            ->get();

        $total = $pathDetails->count();
        if ($total === 0) {
            return [
                'has_path' => true,
                'ten_lo_trinh' => $learningPath->ten_lo_trinh,
                'lesson_count' => 0,
                'progress_percentage' => 0,
                'current_week' => 0,
                'total_weeks' => 0,
                'next_milestone' => 'Chưa có bài trong lộ trình',
            ];
        }

        $completedIds = TienDoBaiHoc::query()
            ->where('hoc_vien_id', $student->id)
            ->where('trang_thai', self::LESSON_STATUS_COMPLETED)
            ->pluck('bai_hoc_id')
            ->all();

        $completedInPath = $pathDetails->whereIn('bai_hoc_id', $completedIds)->count();
        $progressPct = (int) round(($completedInPath / $total) * 100);

        $lessonsPerWeek = max(1, (int) ceil($total / max(1, (int) ceil($total / 4))));
        $currentWeek = min(
            (int) ceil($total / $lessonsPerWeek),
            max(1, (int) ceil($completedInPath / $lessonsPerWeek) + ($completedInPath < $total ? 1 : 0))
        );
        $totalWeeks = (int) ceil($total / $lessonsPerWeek);

        $nextDetail = $pathDetails->first(
            static fn (ChiTietLoTrinh $d): bool => ! in_array((int) $d->bai_hoc_id, $completedIds, true)
        );
        $nextMilestone = $nextDetail?->baiHoc?->tieu_de ?? 'Hoàn thành lộ trình';

        return [
            'has_path' => true,
            'lo_trinh_id' => $learningPath->id,
            'ten_lo_trinh' => $learningPath->ten_lo_trinh,
            'lesson_count' => $total,
            'completed_count' => $completedInPath,
            'progress_percentage' => $progressPct,
            'current_week' => $currentWeek,
            'total_weeks' => $totalWeeks,
            'next_milestone' => $nextMilestone,
            'lessons' => $pathDetails->take(30)->map(static fn (ChiTietLoTrinh $d): array => [
                'bai_hoc_id' => $d->bai_hoc_id,
                'tieu_de' => $d->baiHoc?->tieu_de,
                'thu_tu_uu_tien' => (int) $d->thu_tu_uu_tien,
                'completed' => in_array((int) $d->bai_hoc_id, $completedIds, true),
            ])->values()->all(),
        ];
    }

    public function pronunciationTrend(NguoiDung $student, int $days = 30): string
    {
        $from = Carbon::now()->subDays($days);
        $mid = Carbon::now()->subDays((int) floor($days / 2));

        $firstAvg = (float) (PhienLuyenTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->where(function ($q) use ($from, $mid): void {
                $q->whereBetween('thoi_gian_bat_dau', [$from, $mid])
                    ->orWhereBetween('created_at', [$from, $mid]);
            })
            ->whereNotNull('tong_diem')
            ->avg('tong_diem') ?? 0);

        $secondAvg = (float) (PhienLuyenTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->where(function ($q) use ($mid): void {
                $q->where('thoi_gian_bat_dau', '>=', $mid)
                    ->orWhere('created_at', '>=', $mid);
            })
            ->whereNotNull('tong_diem')
            ->avg('tong_diem') ?? 0);

        if ($secondAvg - $firstAvg >= 3) {
            return 'improving';
        }
        if ($firstAvg - $secondAvg >= 3) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * @return array<string, mixed>
     */
    public function buildStudentDigest(NguoiDung $student): array
    {
        $inventory = $this->learningInventory($student);
        $vocab = $this->vocabularyMastery($student, 7);
        $path = $this->learningPathProgress($student);
        $next = $this->nextLessonRecommendation($student);
        $suggestions = $this->teacherSuggestions($student, 5);

        $teacherCount = (int) QuanHeGvHv::query()
            ->where('hoc_vien_id', $student->id)
            ->count();

        $unreadTeacherMessages = (int) DB::table('chat_messages as cm')
            ->join('chat_sessions as cs', 'cs.id', '=', 'cm.session_id')
            ->where('cs.user_id', $student->id)
            ->whereNotNull('cs.lesson_id')
            ->where('cm.role', 'teacher')
            ->where('cm.is_read_by_student', false)
            ->count();

        return [
            'lessons_completed' => $inventory['completed_count'],
            'lessons_in_progress' => $inventory['in_progress_count'],
            'vocabulary_practiced_7d' => $vocab['practiced_7d'],
            'vocabulary_mastered' => $vocab['mastered_count'],
            'pronunciation_trend' => $this->pronunciationTrend($student),
            'next_lesson_title' => $next['title'] ?? null,
            'next_lesson_id' => $next['bai_hoc_id'] ?? null,
            'path_name' => $path['ten_lo_trinh'] ?? null,
            'path_progress_percentage' => $path['progress_percentage'] ?? null,
            'teacher_count' => $teacherCount,
            'unread_teacher_messages' => $unreadTeacherMessages,
            'unread_teacher_suggestions' => $suggestions['unread_count'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function classOverview(int $teacherId): array
    {
        $studentIds = QuanHeGvHv::query()
            ->where('giao_vien_id', $teacherId)
            ->pluck('hoc_vien_id');

        $studentCount = $studentIds->count();
        $from = Carbon::now()->subDays(7);

        $avgScore = $studentIds->isEmpty()
            ? 0.0
            : round((float) PhienLuyenTap::query()
                ->whereIn('nguoi_dung_id', $studentIds)
                ->where('thoi_gian_bat_dau', '>=', $from)
                ->avg('tong_diem'), 1);

        $unread = $studentIds->isEmpty()
            ? 0
            : (int) DB::table('chat_messages as cm')
                ->join('chat_sessions as cs', 'cs.id', '=', 'cm.session_id')
                ->whereIn('cs.user_id', $studentIds)
                ->whereNotNull('cs.lesson_id')
                ->where('cm.role', 'user')
                ->where('cm.is_read_by_teacher', false)
                ->count();

        return [
            'student_count' => $studentCount,
            'avg_score_7d' => $avgScore,
            'unread_messages' => $unread,
        ];
    }

    /**
     * @return array{
     *   count:int,
     *   students:list<array{hoc_vien_id:int, ho_ten:string, email:string, connected_since:?string}>
     * }
     */
    public function studentsForTeacher(int $teacherId): array
    {
        $rows = DB::table('quan_he_gv_hvs as qh')
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'qh.hoc_vien_id')
            ->where('qh.giao_vien_id', $teacherId)
            ->where('qh.trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
            ->orderBy('nd.ho_ten')
            ->get([
                'nd.id as hoc_vien_id',
                'nd.ho_ten',
                'nd.email',
                'qh.ngay_ket_noi',
            ]);

        $students = $rows->map(static fn ($row): array => [
            'hoc_vien_id' => (int) $row->hoc_vien_id,
            'ho_ten' => (string) ($row->ho_ten ?? ''),
            'email' => (string) ($row->email ?? ''),
            'connected_since' => $row->ngay_ket_noi
                ? Carbon::parse((string) $row->ngay_ket_noi)->format('Y-m-d')
                : null,
        ])->values()->all();

        return [
            'count' => count($students),
            'students' => $students,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function studentLearningSummary(int $teacherId, ?int $hocVienId = null, ?string $nameSearch = null): ?array
    {
        $query = QuanHeGvHv::query()
            ->where('giao_vien_id', $teacherId)
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'quan_he_gv_hvs.hoc_vien_id');

        if ($hocVienId !== null) {
            $query->where('hoc_vien_id', $hocVienId);
        } elseif ($nameSearch !== null && $nameSearch !== '') {
            $query->where('nd.ho_ten', 'like', '%' . $nameSearch . '%');
        }

        $relation = $query->select('nd.id', 'nd.ho_ten')->first();
        if ($relation === null) {
            return null;
        }

        $student = NguoiDung::find($relation->id);
        if ($student === null) {
            return null;
        }

        $inventory = $this->learningInventory($student);
        $from = Carbon::now()->subDays(7);
        $sessions7d = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('thoi_gian_bat_dau', '>=', $from)
            ->count();

        return [
            'hoc_vien_id' => $student->id,
            'ho_ten' => (string) $student->ho_ten,
            'lessons_completed' => $inventory['completed_count'],
            'lessons_in_progress' => $inventory['in_progress_count'],
            'sessions_7d' => $sessions7d,
            'pronunciation_trend' => $this->pronunciationTrend($student),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function pathAssignmentsForTeacher(int $teacherId): array
    {
        return LoTrinhCaNhan::query()
            ->where('giao_vien_id', $teacherId)
            ->withCount('chiTiet')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(static fn (LoTrinhCaNhan $p): array => [
                'lo_trinh_id' => $p->id,
                'ten_lo_trinh' => $p->ten_lo_trinh,
                'hoc_vien_id' => $p->hoc_vien_id,
                'lesson_count' => (int) ($p->chi_tiet_count ?? 0),
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function unreadChatsForTeacher(int $teacherId, int $limit = 10): array
    {
        $studentIds = QuanHeGvHv::query()
            ->where('giao_vien_id', $teacherId)
            ->pluck('hoc_vien_id');

        if ($studentIds->isEmpty()) {
            return [];
        }

        return DB::table('chat_sessions as cs')
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'cs.user_id')
            ->join('chat_messages as cm', 'cm.session_id', '=', 'cs.id')
            ->whereIn('cs.user_id', $studentIds)
            ->whereNotNull('cs.lesson_id')
            ->where('cm.role', 'user')
            ->where('cm.is_read_by_teacher', false)
            ->select('cs.user_id', 'nd.ho_ten', DB::raw('COUNT(cm.id) as unread_count'))
            ->groupBy('cs.user_id', 'nd.ho_ten')
            ->orderByDesc('unread_count')
            ->limit($limit)
            ->get()
            ->map(static fn ($r): array => [
                'hoc_vien_id' => (int) $r->user_id,
                'student_name' => (string) $r->ho_ten,
                'unread_count' => (int) $r->unread_count,
            ])
            ->all();
    }

    /**
     * @return array{total_lessons:int, active_lessons:int, pending_lessons:int, rejected_lessons:int}
     */
    public function lessonStatsForTeacher(int $teacherId): array
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

    /**
     * @return array{bai_hoc_id:int, title:string, cap_do:?string, reason:string}
     */
    private function lessonPayload(BaiHoc $lesson, string $reason): array
    {
        return [
            'bai_hoc_id' => $lesson->id,
            'title' => (string) $lesson->tieu_de,
            'cap_do' => $lesson->cap_do ?? null,
            'reason' => $reason,
        ];
    }
}
