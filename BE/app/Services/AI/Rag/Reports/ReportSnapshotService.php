<?php

namespace App\Services\AI\Rag\Reports;

use App\Models\AiReportSnapshot;
use App\Models\NguoiDung;
use App\Models\PhienKiemTra;
use App\Models\PhienLuyenTap;
use App\Services\AI\Rag\Analytics\LearningAnalyticsRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ReportSnapshotService
{
    public function __construct(
        private readonly LearningAnalyticsRepository $analytics,
        private readonly ReportCloudinaryStorage $cloudinaryStorage,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getOrCreateStudentMonthly(NguoiDung $student, int $monthsBack = 0): array
    {
        [$from, $to] = $this->monthRange($monthsBack);
        $type = 'student_monthly';

        $existing = AiReportSnapshot::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('loai_bao_cao', $type)
            ->whereDate('tu_ngay', $from->toDateString())
            ->whereDate('den_ngay', $to->toDateString())
            ->first();

        if ($existing !== null && $existing->updated_at?->gt(now()->subHours(24))) {
            return $this->serializeSnapshot($existing);
        }

        $payload = $this->buildStudentPayload($student, $from, $to);

        $snapshot = AiReportSnapshot::query()->updateOrCreate(
            [
                'nguoi_dung_id' => $student->id,
                'loai_bao_cao' => $type,
                'tu_ngay' => $from->toDateString(),
                'den_ngay' => $to->toDateString(),
            ],
            ['payload_json' => $payload]
        );

        return $this->serializeSnapshot($snapshot);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOrCreateStudentWeekly(NguoiDung $student, int $weeksBack = 0): array
    {
        [$from, $to] = $this->weekRange($weeksBack);
        $type = 'student_weekly';

        $existing = AiReportSnapshot::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('loai_bao_cao', $type)
            ->whereDate('tu_ngay', $from->toDateString())
            ->whereDate('den_ngay', $to->toDateString())
            ->first();

        if ($existing !== null && $existing->updated_at?->gt(now()->subHours(24))) {
            return $this->serializeSnapshot($existing);
        }

        $payload = $this->buildStudentWeeklyPayload($student, $from, $to);

        $snapshot = AiReportSnapshot::query()->updateOrCreate(
            [
                'nguoi_dung_id' => $student->id,
                'loai_bao_cao' => $type,
                'tu_ngay' => $from->toDateString(),
                'den_ngay' => $to->toDateString(),
            ],
            ['payload_json' => $payload]
        );

        return $this->serializeSnapshot($snapshot);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOrCreatePronunciationChart(NguoiDung $student, int $monthsBack = 0): array
    {
        [$from, $to] = $this->monthRange($monthsBack);
        $type = 'student_pronunciation_chart';

        $existing = AiReportSnapshot::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('loai_bao_cao', $type)
            ->whereDate('tu_ngay', $from->toDateString())
            ->whereDate('den_ngay', $to->toDateString())
            ->first();

        if ($existing !== null && $existing->updated_at?->gt(now()->subHours(24))) {
            return $this->serializeSnapshot($existing);
        }

        $payload = $this->buildPronunciationChartPayload($student, $from, $to);

        $snapshot = AiReportSnapshot::query()->updateOrCreate(
            [
                'nguoi_dung_id' => $student->id,
                'loai_bao_cao' => $type,
                'tu_ngay' => $from->toDateString(),
                'den_ngay' => $to->toDateString(),
            ],
            ['payload_json' => $payload]
        );

        return $this->serializeSnapshot($snapshot);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOrCreateTeacherMonthly(NguoiDung $teacher, int $monthsBack = 0): array
    {
        [$from, $to] = $this->monthRange($monthsBack);
        $type = 'teacher_monthly';

        $existing = AiReportSnapshot::query()
            ->where('nguoi_dung_id', $teacher->id)
            ->where('loai_bao_cao', $type)
            ->whereDate('tu_ngay', $from->toDateString())
            ->whereDate('den_ngay', $to->toDateString())
            ->first();

        if ($existing !== null && $existing->updated_at?->gt(now()->subHours(24))) {
            return $this->serializeSnapshot($existing);
        }

        $payload = $this->buildTeacherPayload($teacher, $from, $to);

        $snapshot = AiReportSnapshot::query()->updateOrCreate(
            [
                'nguoi_dung_id' => $teacher->id,
                'loai_bao_cao' => $type,
                'tu_ngay' => $from->toDateString(),
                'den_ngay' => $to->toDateString(),
            ],
            ['payload_json' => $payload]
        );

        return $this->serializeSnapshot($snapshot);
    }

    public function exportCsv(AiReportSnapshot $snapshot): string
    {
        if ($this->isCloudinaryUrl($snapshot->file_path)) {
            return (string) $snapshot->file_path;
        }

        $payload = (array) ($snapshot->payload_json ?? []);
        $lines = ['metric,value'];
        foreach ($this->flattenPayload($payload) as $key => $value) {
            $lines[] = '"' . str_replace('"', '""', $key) . '","' . str_replace('"', '""', (string) $value) . '"';
        }

        $basename = 'snapshot_' . $snapshot->id . '_' . now()->format('YmdHis');
        $url = $this->cloudinaryStorage->uploadRawFromString(
            implode("\n", $lines),
            'ai_reports/' . $snapshot->nguoi_dung_id,
            $basename,
            'csv'
        );

        $snapshot->update(['file_path' => $url]);

        return $url;
    }

    public function exportPdf(AiReportSnapshot $snapshot): string
    {
        if ($this->isCloudinaryUrl($snapshot->pdf_path)) {
            return (string) $snapshot->pdf_path;
        }

        $payload = (array) ($snapshot->payload_json ?? []);
        $period = ($payload['period']['from'] ?? '') . ' → ' . ($payload['period']['to'] ?? '');
        $loai = (string) $snapshot->loai_bao_cao;

        if ($loai === 'student_pronunciation_chart') {
            $title = 'Biểu đồ phát âm EchoKids';
            $pdfBinary = $this->renderPdfBinary('reports.ai-pronunciation', [
                'title' => $title,
                'period' => $period,
                'payload' => $payload,
            ]);
        } else {
            $title = str_contains($loai, 'teacher')
                ? 'Báo cáo lớp EchoKids'
                : (str_contains($loai, 'weekly') ? 'Báo cáo tuần EchoKids' : 'Báo cáo học tập EchoKids');

            $rows = [];
            foreach ($this->flattenPayload($payload) as $key => $value) {
                $rows[$key] = (string) $value;
            }

            $pdfBinary = $this->renderPdfBinary('reports.ai-monthly', [
                'title' => $title,
                'period' => $period,
                'rows' => $rows,
            ]);
        }

        $basename = 'snapshot_' . $snapshot->id . '_' . now()->format('YmdHis');
        $url = $this->cloudinaryStorage->uploadRawFromString(
            $pdfBinary,
            'ai_reports/' . $snapshot->nguoi_dung_id,
            $basename,
            'pdf'
        );

        $snapshot->update(['pdf_path' => $url]);

        return $url;
    }

    private function isCloudinaryUrl(?string $path): bool
    {
        return $path !== null && str_starts_with($path, 'https://');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function renderPdfBinary(string $view, array $data): string
    {
        $html = View::make($view, $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * @return array{0:Carbon,1:Carbon}
     */
    private function monthRange(int $monthsBack): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths($monthsBack);
        $end = $start->copy()->endOfMonth();

        return [$start, $end];
    }

    /**
     * @return array{0:Carbon,1:Carbon}
     */
    private function weekRange(int $weeksBack): array
    {
        $start = Carbon::now()->startOfWeek()->subWeeks($weeksBack);
        $end = $start->copy()->endOfWeek();

        return [$start, $end];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStudentWeeklyPayload(NguoiDung $student, Carbon $from, Carbon $to): array
    {
        $sessions = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->whereBetween('thoi_gian_bat_dau', [$from, $to]);

        $practiceCount = (clone $sessions)->count();
        $avgScore = round((float) ((clone $sessions)->avg('tong_diem') ?? 0), 1);

        $vocabPracticed = (int) DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->whereBetween('p.thoi_gian_bat_dau', [$from, $to])
            ->distinct()
            ->count('ct.tu_vung_id');

        $errors = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->whereBetween('p.thoi_gian_bat_dau', [$from, $to])
            ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as am_dau')
            ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as van')
            ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as thanh_dieu')
            ->first();

        $exams = PhienKiemTra::query()
            ->where('nguoi_dung_id', $student->id)
            ->where('trang_thai', PhienKiemTra::TRANG_THAI_NOP)
            ->whereBetween('thoi_gian_bat_dau', [$from, $to]);

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'practice_sessions' => $practiceCount,
            'avg_practice_score' => $avgScore,
            'vocabulary_practiced' => $vocabPracticed,
            'pronunciation_errors' => [
                'am_dau' => (int) ($errors->am_dau ?? 0),
                'van' => (int) ($errors->van ?? 0),
                'thanh_dieu' => (int) ($errors->thanh_dieu ?? 0),
                'total' => (int) (($errors->am_dau ?? 0) + ($errors->van ?? 0) + ($errors->thanh_dieu ?? 0)),
            ],
            'exams' => [
                'count' => (clone $exams)->count(),
                'avg_score' => round((float) ((clone $exams)->avg('tong_diem') ?? 0), 1),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPronunciationChartPayload(NguoiDung $student, Carbon $from, Carbon $to): array
    {
        $errors = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->whereBetween('p.thoi_gian_bat_dau', [$from, $to])
            ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as am_dau')
            ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as van')
            ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as thanh_dieu')
            ->first();

        $amDau = (int) ($errors->am_dau ?? 0);
        $van = (int) ($errors->van ?? 0);
        $thanhDieu = (int) ($errors->thanh_dieu ?? 0);
        $total = $amDau + $van + $thanhDieu;

        $pct = static fn (int $n): float => $total > 0 ? round($n / $total * 100, 1) : 0.0;

        $topWords = DB::table('lich_su_loi_phat_ams as ls')
            ->join('tu_vungs as tv', 'tv.id', '=', 'ls.tu_vung_id')
            ->where('ls.nguoi_dung_id', $student->id)
            ->whereBetween('ls.lan_mac_loi_gan_nhat', [$from, $to])
            ->select('tv.tu_chuan', 'ls.loai_loi', 'ls.so_lan_mac_loi')
            ->orderByDesc('ls.so_lan_mac_loi')
            ->limit(10)
            ->get()
            ->map(static fn ($row): array => [
                'word' => (string) $row->tu_chuan,
                'error_type' => (string) $row->loai_loi,
                'count' => (int) $row->so_lan_mac_loi,
            ])
            ->all();

        $weeklyTrend = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $weekEnd = $cursor->copy()->addDays(6)->min($to);
            $weekErrors = DB::table('chi_tiet_luyen_taps as ct')
                ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
                ->where('p.nguoi_dung_id', $student->id)
                ->whereBetween('p.thoi_gian_bat_dau', [$cursor, $weekEnd])
                ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as am_dau')
                ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as van')
                ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as thanh_dieu')
                ->first();

            $weeklyTrend[] = [
                'week' => $cursor->format('Y-m-d'),
                'am_dau' => (int) ($weekErrors->am_dau ?? 0),
                'van' => (int) ($weekErrors->van ?? 0),
                'thanh_dieu' => (int) ($weekErrors->thanh_dieu ?? 0),
            ];
            $cursor->addDays(7);
        }

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'error_distribution' => [
                'am_dau' => ['count' => $amDau, 'percent' => $pct($amDau)],
                'van' => ['count' => $van, 'percent' => $pct($van)],
                'thanh_dieu' => ['count' => $thanhDieu, 'percent' => $pct($thanhDieu)],
                'total' => $total,
            ],
            'top_words' => $topWords,
            'weekly_trend' => $weeklyTrend,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStudentPayload(NguoiDung $student, Carbon $from, Carbon $to): array
    {
        $inventory = $this->analytics->learningInventory($student);
        $vocab = $this->analytics->vocabularyMastery($student, 30);
        $digest = $this->analytics->buildStudentDigest($student);

        $weeklyScores = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $weekEnd = $cursor->copy()->addDays(6)->min($to);
            $avg = (float) (PhienLuyenTap::query()
                ->where('nguoi_dung_id', $student->id)
                ->whereBetween('thoi_gian_bat_dau', [$cursor, $weekEnd])
                ->avg('tong_diem') ?? 0);
            $weeklyScores[] = [
                'week' => $cursor->format('Y-m-d'),
                'avg_score' => round($avg, 1),
            ];
            $cursor->addDays(7);
        }

        $errors = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->whereBetween('p.thoi_gian_bat_dau', [$from, $to])
            ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as am_dau')
            ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as van')
            ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as thanh_dieu')
            ->first();

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'summary' => $digest,
            'lessons_completed' => $inventory['completed_count'],
            'lessons_in_progress' => $inventory['in_progress_count'],
            'vocabulary' => $vocab,
            'weekly_scores' => $weeklyScores,
            'pronunciation_errors' => [
                'am_dau' => (int) ($errors->am_dau ?? 0),
                'van' => (int) ($errors->van ?? 0),
                'thanh_dieu' => (int) ($errors->thanh_dieu ?? 0),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTeacherPayload(NguoiDung $teacher, Carbon $from, Carbon $to): array
    {
        $overview = $this->analytics->classOverview((int) $teacher->id);
        $paths = $this->analytics->pathAssignmentsForTeacher((int) $teacher->id);
        $unread = $this->analytics->unreadChatsForTeacher((int) $teacher->id, 15);

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'class_overview' => $overview,
            'path_assignments' => $paths,
            'students_with_unread' => $unread,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeSnapshot(AiReportSnapshot $snapshot): array
    {
        return [
            'snapshot_id' => $snapshot->id,
            'loai_bao_cao' => $snapshot->loai_bao_cao,
            'tu_ngay' => $snapshot->tu_ngay?->format('Y-m-d'),
            'den_ngay' => $snapshot->den_ngay?->format('Y-m-d'),
            'payload' => $snapshot->payload_json,
            'file_path' => $snapshot->file_path,
            'csv_url' => $snapshot->file_path,
            'pdf_url' => $snapshot->pdf_path,
            'generated_at' => optional($snapshot->updated_at)->toIso8601String(),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, scalar>
     */
    private function flattenPayload(array $payload, string $prefix = ''): array
    {
        $out = [];
        foreach ($payload as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                if (array_is_list($value)) {
                    $out[$fullKey] = json_encode($value, JSON_UNESCAPED_UNICODE);
                } else {
                    $out = array_merge($out, $this->flattenPayload($value, $fullKey));
                }
            } else {
                $out[$fullKey] = $value;
            }
        }

        return $out;
    }
}
