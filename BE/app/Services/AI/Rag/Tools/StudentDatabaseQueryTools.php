<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\BaiHoc;
use App\Models\ChiTietLoTrinh;
use App\Models\LoTrinhCaNhan;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Expanded database query tools for student learning analytics.
 * Includes detailed error analysis, lesson suggestions, and learning path tracking.
 */
class StudentDatabaseQueryTools
{
    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'student_get_detailed_pronunciation_errors',
                'description' => 'Get detailed pronunciation error statistics by type (initial, finals, tones)',
                'args' => [
                    'days' => 'integer, number of days to analyze (default 30)',
                    'limit' => 'integer, max errors to return (default 10)',
                ],
            ],
            [
                'name' => 'student_get_suggested_lessons_by_level',
                'description' => 'Get suggested lessons based on learning level and common mistakes',
                'args' => [
                    'days' => 'integer, analyze mistakes from last N days (default 14)',
                    'limit' => 'integer, max lessons to suggest (default 5)',
                ],
            ],
            [
                'name' => 'student_get_learning_path_progress',
                'description' => 'Get personal learning path progress and next milestones',
                'args' => [],
            ],
            [
                'name' => 'student_get_session_history_with_details',
                'description' => 'Get detailed session history with practice time and scores',
                'args' => [
                    'days' => 'integer, days to retrieve (default 60)',
                    'limit' => 'integer, max sessions (default 20)',
                ],
            ],
            [
                'name' => 'student_get_pronunciation_progress',
                'description' => 'Get pronunciation improvement trend over time',
                'args' => [
                    'days' => 'integer, analyze period in days (default 30)',
                ],
            ],
            [
                'name' => 'student_get_vocabulary_progress',
                'description' => 'Get vocabulary learning progress',
                'args' => [
                    'days' => 'integer, analyze period (default 30)',
                ],
            ],
            [
                'name' => 'student_get_personal_dashboard_data',
                'description' => 'Get personal profile, practice summary, error breakdown and chat activity',
                'args' => [
                    'days' => 'integer, analyze period in days (default 30)',
                ],
            ],
            [
                'name' => 'student_get_access_scope',
                'description' => 'Get role and permission scope for the current student account',
                'args' => [],
            ],
            [
                'name' => 'student_search_vocabulary',
                'description' => 'Tìm kiếm từ vựng, cách phát âm, phiên âm của các từ. Cực kỳ hữu ích khi học viên hỏi cách đọc một từ hoặc âm cụ thể.',
                'args' => [
                    'query' => 'string, từ vựng hoặc phiên âm cần tìm',
                ],
            ],
        ];
    }

    /**
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    public function execute(NguoiDung $student, string $toolName, array $args): array
    {
        return match ($toolName) {
            'student_get_detailed_pronunciation_errors' => $this->getDetailedPronunciationErrors($student, $args),
            'student_get_suggested_lessons_by_level' => $this->getSuggestedLessonsByLevel($student, $args),
            'student_get_learning_path_progress' => $this->getLearningPathProgress($student),
            'student_get_session_history_with_details' => $this->getSessionHistoryWithDetails($student, $args),
            'student_get_pronunciation_progress' => $this->getPronunciationProgress($student, $args),
            'student_get_vocabulary_progress' => $this->getVocabularyProgress($student, $args),
            'student_get_personal_dashboard_data' => $this->getPersonalDashboardData($student, $args),
            'student_get_access_scope' => $this->getAccessScope($student),
            'student_search_vocabulary' => $this->searchVocabulary($args),
            default => [
                'ok' => false,
                'message' => 'Công cụ không được hỗ trợ.',
            ],
        };
    }

    /**
     * Get detailed pronunciation errors by category
     *
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function getDetailedPronunciationErrors(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 30));
        $limit = min(20, max(1, (int) ($args['limit'] ?? 10)));
        $from = Carbon::now()->subDays($days);

        $query = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->where('p.thoi_gian_bat_dau', '>=', $from);

        $errors = [
            'initial_sounds' => (int) (clone $query)->where('ct.loi_am_dau', 1)->count(),
            'finals' => (int) (clone $query)->where('ct.loi_van', 1)->count(),
            'tones' => (int) (clone $query)->where('ct.loi_thanh_dieu', 1)->count(),
        ];

        $totalErrors = array_sum($errors);
        $mostCommon = array_search(max($errors), $errors);

        return [
            'ok' => true,
            'data' => [
                'period_days' => $days,
                'error_categories' => $errors,
                'total_errors' => $totalErrors,
                'most_common_error' => match ($mostCommon) {
                    'initial_sounds' => 'Âm đầu',
                    'finals' => 'Vần',
                    'tones' => 'Thanh điệu',
                    default => 'Không rõ',
                },
                'recommendation' => $this->buildErrorRecommendation($errors),
            ],
        ];
    }

    /**
     * Get suggested lessons based on proficiency level
     *
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function getSuggestedLessonsByLevel(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 14));
        $limit = min(10, max(1, (int) ($args['limit'] ?? 5)));
        $from = Carbon::now()->subDays($days);

        // Get student's average score to determine level
        $avgScore = (float) (PhienLuyenTap::where('nguoi_dung_id', $student->id)
            ->where('thoi_gian_bat_dau', '>=', $from)
            ->avg('tong_diem') ?? 0);

        $level = match (true) {
            $avgScore >= 80 => 'advanced',
            $avgScore >= 60 => 'intermediate',
            default => 'beginner',
        };

        $lessons = BaiHoc::query()
            ->where(function ($q) use ($level) {
                match ($level) {
                    'beginner' => $q->whereIn('cap_do', ['A1', 'A2']),
                    'intermediate' => $q->whereIn('cap_do', ['B1', 'B2']),
                    'advanced' => $q->whereIn('cap_do', ['C1', 'C2']),
                };
            })
            ->limit($limit)
            ->get(['id', 'tieu_de', 'mo_ta', 'cap_do']);

        return [
            'ok' => true,
            'data' => [
                'current_level' => $level,
                'current_avg_score' => round($avgScore, 1),
                'suggested_lessons' => $lessons->map(fn ($l) => [
                    'id' => $l->id,
                    'title' => $l->tieu_de,
                    'description' => $l->mo_ta,
                    'level' => $l->cap_do,
                ])->toArray(),
            ],
        ];
    }

    /**
     * Get learning path progress
     *
     * @return array<string,mixed>
     */
    private function getLearningPathProgress(NguoiDung $student): array
    {
        $learningPath = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', $student->id)
            ->orderByDesc('id')
            ->first();

        if (! $learningPath) {
            return [
                'ok' => true,
                'data' => [
                    'has_path' => false,
                    'message' => 'Chưa có lộ trình cá nhân',
                ],
            ];
        }

        $pathDetails = ChiTietLoTrinh::query()
            ->where('lo_trinh_id', $learningPath->id)
            ->with('baiHoc:id,tieu_de')
            ->orderBy('thu_tu_uu_tien')
            ->get();

        $lessons = $pathDetails->map(fn ($d) => [
            'bai_hoc_id' => $d->bai_hoc_id,
            'tieu_de' => $d->baiHoc?->tieu_de,
            'thu_tu_uu_tien' => (int) $d->thu_tu_uu_tien,
        ]);

        return [
            'ok' => true,
            'data' => [
                'has_path' => true,
                'lo_trinh_id' => $learningPath->id,
                'ten_lo_trinh' => $learningPath->ten_lo_trinh,
                'lesson_count' => $pathDetails->count(),
                'lessons' => $lessons->take(30)->values()->all(),
            ],
        ];
    }

    /**
     * Get session history with detailed statistics
     *
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function getSessionHistoryWithDetails(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 60));
        $limit = min(50, max(1, (int) ($args['limit'] ?? 20)));
        $from = Carbon::now()->subDays($days);

        $sessions = PhienLuyenTap::where('nguoi_dung_id', $student->id)
            ->where('thoi_gian_bat_dau', '>=', $from)
            ->orderByDesc('thoi_gian_bat_dau')
            ->limit($limit)
            ->get(['id', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'tong_diem']);

        return [
            'ok' => true,
            'data' => [
                'period_days' => $days,
                'total_sessions' => $sessions->count(),
                'sessions' => $sessions->map(function ($session) {
                    $duration = $session->thoi_gian_ket_thuc
                        ? $session->thoi_gian_bat_dau->diffInMinutes($session->thoi_gian_ket_thuc)
                        : 0;

                    return [
                        'id' => $session->id,
                        'start_time' => $session->thoi_gian_bat_dau->format('Y-m-d H:i'),
                        'duration_minutes' => $duration,
                        'score' => $session->tong_diem,
                    ];
                })->toArray(),
            ],
        ];
    }

    /**
     * Get pronunciation improvement trend
     *
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function getPronunciationProgress(NguoiDung $student, array $args): array
    {
        $days = max(7, (int) ($args['days'] ?? 30));
        $from = Carbon::now()->subDays($days);

        $weeklyScores = [];
        for ($i = 0; $i < $days; $i += 7) {
            $weekStart = Carbon::now()->subDays($days - $i);
            $weekEnd = $weekStart->copy()->addDays(7);

            $avgScore = (float) (PhienLuyenTap::where('nguoi_dung_id', $student->id)
                ->whereBetween('thoi_gian_bat_dau', [$weekStart, $weekEnd])
                ->avg('tong_diem') ?? 0);

            $weeklyScores[] = [
                'week_start' => $weekStart->format('Y-m-d'),
                'avg_score' => round($avgScore, 1),
            ];
        }

        $trend = 'stable';
        if (count($weeklyScores) >= 2) {
            $firstHalf = collect($weeklyScores)->take(ceil(count($weeklyScores) / 2))->avg('avg_score');
            $secondHalf = collect($weeklyScores)->skip(ceil(count($weeklyScores) / 2))->avg('avg_score');
            $trend = $secondHalf > $firstHalf ? 'improving' : ($secondHalf < $firstHalf ? 'declining' : 'stable');
        }

        return [
            'ok' => true,
            'data' => [
                'period_days' => $days,
                'trend' => $trend,
                'weekly_progress' => $weeklyScores,
            ],
        ];
    }

    /**
     * Get vocabulary learning progress
     *
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function getVocabularyProgress(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 30));
        $from = Carbon::now()->subDays($days);

        // Count unique vocabulary items practiced
        $vocabCount = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->where('p.thoi_gian_bat_dau', '>=', $from)
            ->distinct('ct.tu_vung_id')
            ->count('ct.tu_vung_id');

        return [
            'ok' => true,
            'data' => [
                'period_days' => $days,
                'vocabulary_items_practiced' => $vocabCount,
                'avg_per_week' => round($vocabCount / max(1, ($days / 7)), 1),
            ],
        ];
    }

    /**
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function getPersonalDashboardData(NguoiDung $student, array $args): array
    {
        $days = max(1, (int) ($args['days'] ?? 30));
        $from = Carbon::now()->subDays($days);

        $practiceSummary = DB::table('phien_luyen_taps as p')
            ->where('p.nguoi_dung_id', $student->id)
            ->where('p.thoi_gian_bat_dau', '>=', $from)
            ->selectRaw('COUNT(*) as total_sessions')
            ->selectRaw('COALESCE(AVG(p.tong_diem), 0) as avg_score')
            ->selectRaw('COALESCE(MAX(p.tong_diem), 0) as best_score')
            ->first();

        $errorSummary = DB::table('chi_tiet_luyen_taps as ct')
            ->join('phien_luyen_taps as p', 'p.id', '=', 'ct.phien_id')
            ->where('p.nguoi_dung_id', $student->id)
            ->where('p.thoi_gian_bat_dau', '>=', $from)
            ->selectRaw('SUM(CASE WHEN ct.loi_am_dau = 1 THEN 1 ELSE 0 END) as loi_am_dau')
            ->selectRaw('SUM(CASE WHEN ct.loi_van = 1 THEN 1 ELSE 0 END) as loi_van')
            ->selectRaw('SUM(CASE WHEN ct.loi_thanh_dieu = 1 THEN 1 ELSE 0 END) as loi_thanh_dieu')
            ->first();

        $learningPath = DB::table('lo_trinh_ca_nhans as ltc')
            ->leftJoin('chi_tiet_lo_trinhs as ctl', 'ctl.lo_trinh_id', '=', 'ltc.id')
            ->where('ltc.hoc_vien_id', $student->id)
            ->selectRaw('ltc.id, COUNT(ctl.bai_hoc_id) as so_bai_trong_lo_trinh')
            ->groupBy('ltc.id')
            ->orderByDesc('ltc.id')
            ->first();

        $chatActivity = DB::table('chat_sessions as cs')
            ->leftJoin('chat_messages as cm', 'cm.session_id', '=', 'cs.id')
            ->where('cs.user_id', $student->id)
            ->where('cm.created_at', '>=', $from)
            ->selectRaw('COUNT(DISTINCT cs.id) as total_chat_sessions')
            ->selectRaw('COUNT(cm.id) as total_chat_messages')
            ->first();

        return [
            'ok' => true,
            'data' => [
                'days' => $days,
                'student' => [
                    'id' => $student->id,
                    'name' => (string) ($student->ho_ten ?? ''),
                    'role_id' => (int) ($student->vai_tro_id ?? 0),
                ],
                'practice' => [
                    'total_sessions' => (int) ($practiceSummary?->total_sessions ?? 0),
                    'avg_score' => round((float) ($practiceSummary?->avg_score ?? 0), 1),
                    'best_score' => (int) ($practiceSummary?->best_score ?? 0),
                ],
                'errors' => [
                    'loi_am_dau' => (int) ($errorSummary?->loi_am_dau ?? 0),
                    'loi_van' => (int) ($errorSummary?->loi_van ?? 0),
                    'loi_thanh_dieu' => (int) ($errorSummary?->loi_thanh_dieu ?? 0),
                ],
                'learning_path' => [
                    'has_path' => $learningPath !== null,
                    'lo_trinh_id' => $learningPath ? (int) $learningPath->id : null,
                    'lesson_count' => (int) ($learningPath?->so_bai_trong_lo_trinh ?? 0),
                ],
                'chat' => [
                    'total_sessions' => (int) ($chatActivity?->total_chat_sessions ?? 0),
                    'total_messages' => (int) ($chatActivity?->total_chat_messages ?? 0),
                ],
            ],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function getAccessScope(NguoiDung $student): array
    {
        $permissions = DB::table('vai_tro_quyens as vtq')
            ->join('quyens as q', 'q.id', '=', 'vtq.quyen_id')
            ->where('vtq.vai_tro_id', $student->vai_tro_id)
            ->orderBy('q.id')
            ->get(['q.id', 'q.ten_quyen'])
            ->map(static fn ($row): array => [
                'id' => (int) ($row->id ?? 0),
                'name' => (string) ($row->ten_quyen ?? ''),
            ])
            ->values()
            ->all();

        return [
            'ok' => true,
            'data' => [
                'user_id' => (int) $student->id,
                'role_id' => (int) $student->vai_tro_id,
                'permission_count' => count($permissions),
                'permissions' => $permissions,
            ],
        ];
    }

    /**
     * Build recommendation based on error types
     *
     * @param  array<string,int>  $errors
     */
    private function buildErrorRecommendation(array $errors): string
    {
        $recommendations = [];

        if ($errors['initial_sounds'] > 5) {
            $recommendations[] = 'Luyện tập thêm về âm đầu';
        }
        if ($errors['finals'] > 5) {
            $recommendations[] = 'Luyện tập thêm về vần';
        }
        if ($errors['tones'] > 5) {
            $recommendations[] = 'Luyện tập thêm về thanh điệu';
        }

        return implode(', ', $recommendations) ?: 'Tiếp tục duy trì tốc độ học hiện tại';
    }

    /**
     * Search vocabulary by query
     *
     * @param  array<string,mixed>  $args
     * @return array<string,mixed>
     */
    private function searchVocabulary(array $args): array
    {
        $query = (string) ($args['query'] ?? '');
        if (empty($query)) {
            return [
                'ok' => false,
                'message' => 'Vui lòng cung cấp từ khóa tìm kiếm.',
            ];
        }

        $results = DB::table('tu_vungs')
            ->where('tu_chuan', 'like', "%{$query}%")
            ->orWhere('phien_am', 'like', "%{$query}%")
            ->limit(50)
            ->get(['id', 'tu_chuan', 'phien_am', 'cap_do', 'am_thanh_mau_url', 'hinh_anh_url']);

        $lowerQuery = mb_strtolower($query, 'UTF-8');
        $filtered = $results->filter(function ($item) use ($lowerQuery) {
            $tuChuanLower = mb_strtolower($item->tu_chuan, 'UTF-8');
            $phienAmLower = mb_strtolower($item->phien_am, 'UTF-8');

            return $tuChuanLower === $lowerQuery ||
                   $phienAmLower === $lowerQuery ||
                   str_contains($tuChuanLower, $lowerQuery) ||
                   str_contains($phienAmLower, $lowerQuery);
        })->values();

        // Ưu tiên khớp chính xác tuyệt đối
        $exactMatches = $filtered->filter(function ($item) use ($lowerQuery) {
            return mb_strtolower($item->tu_chuan, 'UTF-8') === $lowerQuery;
        })->values();

        $finalResults = $exactMatches->isNotEmpty() ? $exactMatches : $filtered;

        if ($finalResults->isEmpty()) {
            return [
                'ok' => true,
                'data' => [
                    'found' => false,
                    'message' => "Cô không tìm thấy từ '{$query}' trong hệ thống.",
                ],
            ];
        }

        return [
            'ok' => true,
            'data' => [
                'found' => true,
                'query' => $query,
                'results' => $finalResults->take(5)->map(fn ($item) => [
                    'tu_chuan' => $item->tu_chuan,
                    'phien_am' => $item->phien_am,
                    'cap_do' => $item->cap_do,
                ])->toArray(),
            ],
        ];
    }
}
