<?php

namespace App\Services\AI\Rag\Pipelines;

use App\Models\BaiHoc;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use App\Models\TienDoBaiHoc;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GreetingContextService
{
    // Ngưỡng điểm được coi là "học tốt"
    private const HIGH_SCORE_THRESHOLD = 75;

    // Số ngày vắng để xét Case C
    private const ABSENT_DAYS_THRESHOLD = 2;

    /**
     * Phân tích ngữ cảnh học tập của user và trả về dữ liệu để sinh proactive greeting.
     *
     * @return array{
     *   case: string,
     *   last_lesson_title: string|null,
     *   last_lesson_id: int|null,
     *   last_score: int|null,
     *   avg_score_recent: float|null,
     *   days_absent: int,
     *   top_error: string|null,
     *   next_lesson_title: string|null,
     *   next_lesson_id: int|null,
     *   suggestions: list<array{label:string, action:string}>
     * }
     */
    public function resolve(NguoiDung $user): array
    {
        $lastSession = $this->getLastSession($user);

        // Case: Ngày 1 — chưa có phiên luyện nào
        if ($lastSession === null) {
            return $this->buildDay1Context();
        }

        $daysAbsent = $this->calcDaysAbsent($lastSession);
        $lastLesson = $lastSession->bai_hoc_id
            ? BaiHoc::find($lastSession->bai_hoc_id)
            : null;

        // Case C: Vắng > 2 ngày
        if ($daysAbsent > self::ABSENT_DAYS_THRESHOLD) {
            return $this->buildCaseCContext($lastLesson, $daysAbsent);
        }

        $isFirstSessionToday = $this->isFirstSessionToday($user);

        // Case: Ngày 2 — có phiên hôm qua, hôm nay chưa học
        if ($isFirstSessionToday && $daysAbsent === 1) {
            $nextLesson = $this->getNextLesson($user, $lastSession->bai_hoc_id);
            return $this->buildDay2Context($lastLesson, $lastSession, $nextLesson);
        }

        // Case A hoặc B — đã học nhiều ngày, xét điểm và lỗi
        $avgScore = $this->getAvgScoreRecentDays($user, 5);
        $topError = $this->getTopError($user, 7);

        if ($avgScore !== null && $avgScore >= self::HIGH_SCORE_THRESHOLD && $topError === null) {
            return $this->buildCaseAContext($avgScore);
        }

        return $this->buildCaseBContext($topError, $lastLesson);
    }

    // ─── Private helpers ───────────────────────────────────────────────────

    private function getLastSession(NguoiDung $user): ?PhienLuyenTap
    {
        return PhienLuyenTap::query()
            ->where('nguoi_dung_id', $user->id)
            ->orderByDesc('id')
            ->first();
    }

    private function calcDaysAbsent(PhienLuyenTap $lastSession): int
    {
        $lastDate = null;
        foreach (['thoi_gian_ket_thuc', 'thoi_gian_bat_dau', 'ngay_tao'] as $field) {
            if (!empty($lastSession->{$field})) {
                $lastDate = Carbon::parse((string) $lastSession->{$field});
                break;
            }
        }

        if ($lastDate === null) {
            return 0;
        }

        return (int) $lastDate->startOfDay()->diffInDays(Carbon::today());
    }

    private function isFirstSessionToday(NguoiDung $user): bool
    {
        $count = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $user->id)
            ->where(function ($q): void {
                $today = Carbon::today();
                $q->whereDate('thoi_gian_bat_dau', $today)
                  ->orWhereDate('ngay_tao', $today);
            })
            ->count();

        return $count === 0;
    }

    private function getNextLesson(NguoiDung $user, ?int $lastLessonId): ?BaiHoc
    {
        if ($lastLessonId === null) {
            return BaiHoc::where('trang_thai', 'approved')->orderBy('thu_tu')->first();
        }

        $lastLesson = BaiHoc::find($lastLessonId);
        if ($lastLesson === null) {
            return null;
        }

        // Tìm bài tiếp theo cùng danh mục, có thu_tu lớn hơn
        $next = BaiHoc::where('trang_thai', 'approved')
            ->where('danh_muc_id', $lastLesson->danh_muc_id)
            ->where('thu_tu', '>', $lastLesson->thu_tu)
            ->orderBy('thu_tu')
            ->first();

        // Nếu không có, lấy bài đầu danh mục khác chưa học
        if ($next === null) {
            $studiedIds = TienDoBaiHoc::where('hoc_vien_id', $user->id)
                ->where('trang_thai', 'hoan_thanh')
                ->pluck('bai_hoc_id')
                ->all();

            $next = BaiHoc::where('trang_thai', 'approved')
                ->whereNotIn('id', $studiedIds)
                ->orderBy('thu_tu')
                ->first();
        }

        return $next;
    }

    private function getAvgScoreRecentDays(NguoiDung $user, int $days): ?float
    {
        $from = Carbon::now()->subDays($days);
        $avg = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $user->id)
            ->where(function ($q) use ($from): void {
                $q->where('thoi_gian_bat_dau', '>=', $from)
                  ->orWhere('ngay_tao', '>=', $from);
            })
            ->whereNotNull('tong_diem')
            ->avg('tong_diem');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * Trả về tên nhóm lỗi phát âm phổ biến nhất trong N ngày gần đây.
     */
    private function getTopError(NguoiDung $user, int $days): ?string
    {
        $from = Carbon::now()->subDays($days);
        $row = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $user->id)
            ->where(function ($q) use ($from): void {
                $q->where('p.thoi_gian_bat_dau', '>=', $from)
                  ->orWhere('p.ngay_tao', '>=', $from);
            })
            ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as am_dau')
            ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as van')
            ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as thanh_dieu')
            ->first();

        if ($row === null) {
            return null;
        }

        $amDau = (int) ($row->am_dau ?? 0);
        $van = (int) ($row->van ?? 0);
        $thanhDieu = (int) ($row->thanh_dieu ?? 0);

        $total = $amDau + $van + $thanhDieu;
        if ($total === 0) {
            return null;
        }

        if ($amDau >= $van && $amDau >= $thanhDieu) {
            return 'âm đầu';
        }
        if ($van >= $amDau && $van >= $thanhDieu) {
            return 'vần';
        }

        return 'thanh điệu';
    }

    // ─── Context builders ──────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function buildDay1Context(): array
    {
        return [
            'case'               => 'day1',
            'last_lesson_title'  => null,
            'last_lesson_id'     => null,
            'last_score'         => null,
            'avg_score_recent'   => null,
            'days_absent'        => 0,
            'top_error'          => null,
            'next_lesson_title'  => null,
            'next_lesson_id'     => null,
            'suggestions'        => [
                ['label' => 'Học âm đầu tiên', 'action' => 'start_lesson'],
                ['label' => 'Hướng dẫn tôi cách học', 'action' => 'guide_how_to_learn'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDay2Context(?BaiHoc $lastLesson, PhienLuyenTap $lastSession, ?BaiHoc $nextLesson): array
    {
        return [
            'case'               => 'day2',
            'last_lesson_title'  => $lastLesson?->tieu_de,
            'last_lesson_id'     => $lastLesson?->id,
            'last_score'         => (int) ($lastSession->tong_diem ?? 0),
            'avg_score_recent'   => null,
            'days_absent'        => 1,
            'top_error'          => null,
            'next_lesson_title'  => $nextLesson?->tieu_de,
            'next_lesson_id'     => $nextLesson?->id,
            'suggestions'        => array_filter([
                $nextLesson ? ['label' => 'Tiếp tục học bài ' . $nextLesson->tieu_de, 'action' => 'continue_lesson'] : null,
                $lastLesson ? ['label' => 'Ôn lại bài ' . $lastLesson->tieu_de, 'action' => 'review_lesson'] : null,
                ['label' => 'Xem điểm hôm qua', 'action' => 'view_score'],
            ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCaseAContext(float $avgScore): array
    {
        return [
            'case'               => 'case_a',
            'last_lesson_title'  => null,
            'last_lesson_id'     => null,
            'last_score'         => null,
            'avg_score_recent'   => $avgScore,
            'days_absent'        => 0,
            'top_error'          => null,
            'next_lesson_title'  => null,
            'next_lesson_id'     => null,
            'suggestions'        => [
                ['label' => 'Thử thách khó hơn', 'action' => 'hard_challenge'],
                ['label' => 'Xem bảng xếp hạng', 'action' => 'view_leaderboard'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCaseBContext(?string $topError, ?BaiHoc $lastLesson): array
    {
        $errorLabel = $topError ?? 'phát âm';
        return [
            'case'               => 'case_b',
            'last_lesson_title'  => $lastLesson?->tieu_de,
            'last_lesson_id'     => $lastLesson?->id,
            'last_score'         => null,
            'avg_score_recent'   => null,
            'days_absent'        => 0,
            'top_error'          => $topError,
            'next_lesson_title'  => null,
            'next_lesson_id'     => null,
            'suggestions'        => [
                ['label' => 'Luyện lại ' . $errorLabel, 'action' => 'practice_error'],
                ['label' => 'Học bài mới', 'action' => 'new_lesson'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCaseCContext(?BaiHoc $lastLesson, int $daysAbsent): array
    {
        return [
            'case'               => 'case_c',
            'last_lesson_title'  => $lastLesson?->tieu_de,
            'last_lesson_id'     => $lastLesson?->id,
            'last_score'         => null,
            'avg_score_recent'   => null,
            'days_absent'        => $daysAbsent,
            'top_error'          => null,
            'next_lesson_title'  => null,
            'next_lesson_id'     => null,
            'suggestions'        => [
                ['label' => 'Khởi động nhẹ', 'action' => 'warm_up'],
                $lastLesson ? ['label' => 'Tiếp tục bài ' . $lastLesson->tieu_de, 'action' => 'continue_lesson'] : ['label' => 'Bắt đầu bài mới', 'action' => 'start_lesson'],
            ],
        ];
    }
}
