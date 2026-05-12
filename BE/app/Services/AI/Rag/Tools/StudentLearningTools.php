<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\ChiTietLuyenTap;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StudentLearningTools
{
    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'student_get_my_progress_summary',
                'description' => 'Get my learning progress summary in N recent days',
                'args' => [
                    'days' => 'integer, default 7',
                ],
            ],
            [
                'name' => 'student_get_my_practice_suggestion',
                'description' => 'Get next practice suggestion based on my recent pronunciation mistakes',
                'args' => [
                    'days' => 'integer, default 14',
                ],
            ],
            [
                'name' => 'student_get_top_learners_public',
                'description' => 'Get top learners by total practice hours (public summary, no personal details)',
                'args' => [
                    'limit' => 'integer, max 10, default 5',
                ],
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
            'student_get_my_progress_summary' => $this->myProgressSummary($student, $args),
            'student_get_my_practice_suggestion' => $this->myPracticeSuggestion($student, $args),
            'student_get_top_learners_public' => $this->getTopLearnersPublic($args),
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
    private function myProgressSummary(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 7));
        $from = Carbon::now()->subDays($days);

        $query = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $student->id)
            ->where(function ($q) use ($from): void {
                $q->where('thoi_gian_bat_dau', '>=', $from)
                    ->orWhere('thoi_gian_ket_thuc', '>=', $from)
                    ->orWhere('created_at', '>=', $from);
            });

        $sessionCount = (int) (clone $query)->count();
        $avgScore = (float) ((clone $query)->whereNotNull('tong_diem')->avg('tong_diem') ?? 0);
        $bestScore = (int) ((clone $query)->max('tong_diem') ?? 0);
        $latest = (clone $query)->orderByDesc('id')->first();

        return [
            'ok' => true,
            'data' => [
                'days' => $days,
                'session_count' => $sessionCount,
                'avg_score' => round($avgScore, 1),
                'best_score' => $bestScore,
                'latest_score' => (int) ($latest?->tong_diem ?? 0),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    private function myPracticeSuggestion(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 14));
        $from = Carbon::now()->subDays($days);
        $rows = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->where(function ($q) use ($from): void {
                $q->where('p.thoi_gian_bat_dau', '>=', $from)
                    ->orWhere('p.thoi_gian_ket_thuc', '>=', $from)
                    ->orWhere('p.created_at', '>=', $from);
            })
            ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as loi_am_dau')
            ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as loi_van')
            ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as loi_thanh_dieu')
            ->first();

        $am = (int) ($rows?->loi_am_dau ?? 0);
        $van = (int) ($rows?->loi_van ?? 0);
        $thanh = (int) ($rows?->loi_thanh_dieu ?? 0);

        $focus = 'luyện đều 3 nhóm âm đầu, vần và thanh điệu';
        if ($am >= $van && $am >= $thanh) {
            $focus = 'ưu tiên luyện âm đầu';
        } elseif ($van >= $am && $van >= $thanh) {
            $focus = 'ưu tiên luyện vần';
        } elseif ($thanh >= $am && $thanh >= $van) {
            $focus = 'ưu tiên luyện thanh điệu';
        }

        return [
            'ok' => true,
            'data' => [
                'days' => $days,
                'mistakes' => [
                    'loi_am_dau' => $am,
                    'loi_van' => $van,
                    'loi_thanh_dieu' => $thanh,
                ],
                'focus' => $focus,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    private function getTopLearnersPublic(array $args): array
    {
        $limit = min(10, max(1, (int) ($args['limit'] ?? 5)));
        $rows = DB::table('phien_luyen_taps as p')
            ->join('nguoi_dungs as nd', 'nd.id', '=', 'p.nguoi_dung_id')
            ->where('nd.vai_tro_id', NguoiDung::ROLE_USER)
            ->whereNotNull('p.thoi_gian_bat_dau')
            ->whereNotNull('p.thoi_gian_ket_thuc')
            ->get(['p.nguoi_dung_id', 'p.thoi_gian_bat_dau', 'p.thoi_gian_ket_thuc']);

        $secondsByUser = [];
        foreach ($rows as $row) {
            $userId = (int) ($row->nguoi_dung_id ?? 0);
            if ($userId <= 0) {
                continue;
            }
            try {
                $start = Carbon::parse((string) $row->thoi_gian_bat_dau);
                $end = Carbon::parse((string) $row->thoi_gian_ket_thuc);
                $seconds = max(0, $start->diffInSeconds($end, false));
            } catch (\Throwable $e) {
                $seconds = 0;
            }
            $secondsByUser[$userId] = ($secondsByUser[$userId] ?? 0) + $seconds;
        }

        arsort($secondsByUser);
        $top = collect(array_slice($secondsByUser, 0, $limit, true));

        $data = $top->values()->map(function ($totalSeconds, $index) {
            return [
                'rank' => $index + 1,
                'total_hours' => round(((int) $totalSeconds) / 3600, 1),
            ];
        })->values();

        return [
            'ok' => true,
            'data' => $data,
        ];
    }
}

