<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ChiTietLuyenTap;
use App\Models\CauHinhHeThong;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use App\Models\ThongBao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function realtime(): JsonResponse
    {
        $totalUsers = NguoiDung::count();
        $activeUsers = $this->countActiveUsers();
        $lockedUsers = $this->countLockedUsers();
        $todaySessions = PhienLuyenTap::whereDate('created_at', Carbon::today())->count();
        $todayErrorDetails = ChiTietLuyenTap::query()
            ->whereDate('created_at', Carbon::today())
            ->where(function ($query): void {
                $query->where('loi_am_dau', true)
                    ->orWhere('loi_van', true)
                    ->orWhere('loi_thanh_dieu', true);
            })
            ->count();

        $unreadNotices = ThongBao::where('da_doc', 0)->count();
        $chatStats = $this->getChatSupportStats();
        $dbStats = $this->getDatabaseRuntimeStats();
        $lastBackup = $this->getLastBackupLabel();
        [$apiUsed, $apiLimit] = $this->resolveApiQuotaMetrics($todaySessions, $todayErrorDetails);

        return response()->json([
            'status' => true,
            'data' => [
                'api_health' => [
                    'status' => $todayErrorDetails > 150 ? 'degraded' : 'healthy',
                    'uptime' => $todayErrorDetails > 150 ? 96.5 : 99.7,
                ],
                'key_metrics' => [
                    'api_used' => $apiUsed,
                    'api_limit' => $apiLimit,
                ],
                'system_metrics' => [
                    'errorCount' => $todayErrorDetails,
                    'errorTrend' => $todayErrorDetails > 20 ? '+ so với hôm qua' : 'Ổn định',
                    'dbStatus' => 'connected',
                    'dbConnections' => $dbStats['connections'],
                    'lastUpdatedText' => 'Cập nhật vừa xong',
                    'dbOptimization' => $dbStats['optimization'],
                    'slowQueries' => $dbStats['slowQueries'],
                    'dbSize' => $dbStats['dbSize'],
                    'lastBackup' => $lastBackup,
                    'activeUsers' => $activeUsers,
                    'lockedUsers' => $lockedUsers,
                    'unreadNotices' => $unreadNotices,
                ],
                'chat_support' => $chatStats,
                'alerts' => [
                    [
                        'id' => 1,
                        'icon' => 'fa-solid fa-users',
                        'type' => $lockedUsers > 0 ? 'warning' : 'info',
                        'tieu_de' => 'Tài khoản đang bị khóa',
                        'mo_ta' => $lockedUsers . ' tài khoản đang ở trạng thái tạm khóa.',
                        'thoi_gian' => 'vừa xong',
                    ],
                    [
                        'id' => 2,
                        'icon' => 'fa-solid fa-bell',
                        'type' => $unreadNotices > 5 ? 'warning' : 'info',
                        'tieu_de' => 'Thông báo chưa đọc',
                        'mo_ta' => $unreadNotices . ' thông báo chưa được xử lý.',
                        'thoi_gian' => 'vừa xong',
                    ],
                    [
                        'id' => 3,
                        'icon' => 'fa-solid fa-triangle-exclamation',
                        'type' => $todayErrorDetails > 50 ? 'error' : 'warning',
                        'tieu_de' => 'Lỗi phát âm hôm nay',
                        'mo_ta' => 'Ghi nhận ' . $todayErrorDetails . ' lượt lỗi chi tiết trong ngày.',
                        'thoi_gian' => 'hôm nay',
                    ],
                ],
            ],
        ]);
    }

    public function reports(Request $request): JsonResponse
    {
        [$startDate, $endDate] = $this->resolveRange($request);
        $serviceFilter = (string) $request->query('service', '');

        $sessionsQuery = PhienLuyenTap::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        $detailsQuery = ChiTietLuyenTap::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalRequests = (clone $sessionsQuery)->count();
        $errorCount = (clone $detailsQuery)
            ->where(function ($query): void {
                $query->where('loi_am_dau', true)
                    ->orWhere('loi_van', true)
                    ->orWhere('loi_thanh_dieu', true);
            })->count();
        $successRate = $totalRequests > 0
            ? round((($totalRequests - min($errorCount, $totalRequests)) / $totalRequests) * 100, 2)
            : 100;
        $avgScore = (float) ((clone $detailsQuery)->avg('diem_chinh_xac') ?? 0);

        $serviceHealth = $this->buildServiceHealth($totalRequests, $errorCount, $avgScore, $serviceFilter);
        $incidentSummary = $this->buildIncidentSummary($detailsQuery, $totalRequests);
        $topErrors = $this->buildTopErrors($detailsQuery);

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => [
                    'totalRequests' => $totalRequests,
                    'successRate' => $successRate,
                    'errorCount' => $errorCount,
                    'avgResponseTime' => max(30, 260 - (int) round($avgScore)),
                ],
                'serviceHealth' => $serviceHealth,
                'incidentSummary' => $incidentSummary,
                'topErrors' => $topErrors,
                'range' => [
                    'startDate' => $startDate->toDateString(),
                    'endDate' => $endDate->toDateString(),
                ],
            ],
        ]);
    }

    public function performance(Request $request): JsonResponse
    {
        $period = (string) $request->query('period', 'thang');
        $result = [
            'labels' => [],
            'data' => [],
        ];

        if ($period === 'tuan') {
            for ($i = 6; $i >= 0; $i--) {
                $day = Carbon::today()->subDays($i);
                $result['labels'][] = 'T' . $day->isoWeekday();
                $result['data'][] = PhienLuyenTap::whereDate('created_at', $day)->count();
            }
        } elseif ($period === 'nam') {
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $result['labels'][] = 'T' . $month->month;
                $result['data'][] = PhienLuyenTap::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
            }
        } else {
            for ($i = 3; $i >= 0; $i--) {
                $start = Carbon::now()->startOfMonth()->subWeeks($i);
                $end = (clone $start)->copy()->endOfWeek();
                $result['labels'][] = 'Tuần ' . (4 - $i);
                $result['data'][] = PhienLuyenTap::whereBetween('created_at', [$start, $end])->count();
            }
        }

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    }

    public function export(Request $request): Response
    {
        $reportData = $this->reports($request)->getData(true);
        $summary = $reportData['data']['summary'] ?? [];
        $serviceHealth = $reportData['data']['serviceHealth'] ?? [];

        $rows = [
            ['Metric', 'Value'],
            ['Total Requests', (string) ($summary['totalRequests'] ?? 0)],
            ['Success Rate', (string) ($summary['successRate'] ?? 0)],
            ['Errors', (string) ($summary['errorCount'] ?? 0)],
            ['Avg Response Time', (string) ($summary['avgResponseTime'] ?? 0)],
            [],
            ['Service', 'Status', 'Uptime', 'Requests/Day', 'Avg Response', 'Error Rate'],
        ];

        foreach ($serviceHealth as $service) {
            $rows[] = [
                (string) ($service['name'] ?? ''),
                (string) ($service['status'] ?? ''),
                (string) ($service['uptime'] ?? ''),
                (string) ($service['requestsPerDay'] ?? 0),
                (string) ($service['avgResponse'] ?? 0),
                (string) ($service['errorRate'] ?? 0),
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $escaped = array_map(static function ($field): string {
                $fieldStr = (string) $field;
                $fieldStr = str_replace('"', '""', $fieldStr);
                return '"' . $fieldStr . '"';
            }, $row);
            $csv .= implode(',', $escaped) . "\n";
        }

        $startDate = (string) $request->query('startDate', Carbon::now()->startOfMonth()->toDateString());
        $endDate = (string) $request->query('endDate', Carbon::now()->toDateString());
        $filename = "admin-report-{$startDate}-to-{$endDate}.csv";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function resolveRange(Request $request): array
    {
        $startRaw = $request->query('startDate');
        $endRaw = $request->query('endDate');

        $startDate = $startRaw ? Carbon::parse($startRaw)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $endRaw ? Carbon::parse($endRaw)->endOfDay() : Carbon::now()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        return [$startDate, $endDate];
    }

    private function buildServiceHealth(int $totalRequests, int $errorCount, float $avgScore, string $serviceFilter): array
    {
        $baseRows = [
            ['id' => 1, 'name' => 'API Server', 'requestsRatio' => 0.45, 'responseBase' => 140, 'errorFactor' => 1.1],
            ['id' => 2, 'name' => 'Database', 'requestsRatio' => 0.2, 'responseBase' => 220, 'errorFactor' => 0.8],
            ['id' => 3, 'name' => 'Cache', 'requestsRatio' => 0.12, 'responseBase' => 45, 'errorFactor' => 0.5],
            ['id' => 4, 'name' => 'Auth', 'requestsRatio' => 0.13, 'responseBase' => 170, 'errorFactor' => 0.6],
            ['id' => 5, 'name' => 'Queue', 'requestsRatio' => 0.1, 'responseBase' => 210, 'errorFactor' => 0.7],
        ];

        $rows = array_map(function (array $row) use ($totalRequests, $errorCount, $avgScore) {
            $requests = (int) round($totalRequests * $row['requestsRatio']);
            $serviceErrors = (int) round($errorCount * $row['errorFactor'] * 0.2);
            $errorRate = $requests > 0 ? round(($serviceErrors / $requests) * 100, 2) : 0;
            $avgResponse = max(15, (int) round($row['responseBase'] + (100 - $avgScore) * 0.6));
            $uptime = max(90, round(100 - min(8, $errorRate), 2));
            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'status' => $errorRate > 5 ? 'offline' : 'online',
                'uptime' => $uptime,
                'requestsPerDay' => $requests,
                'avgResponse' => $avgResponse,
                'errorRate' => $errorRate,
            ];
        }, $baseRows);

        if ($serviceFilter !== '') {
            $needle = mb_strtolower($serviceFilter);
            $rows = array_values(array_filter($rows, static function (array $row) use ($needle) {
                return str_contains(mb_strtolower($row['name']), $needle);
            }));
        }

        return $rows;
    }

    private function buildIncidentSummary($detailsQuery, int $totalRequests): array
    {
        $amDau = (clone $detailsQuery)->where('loi_am_dau', true)->count();
        $van = (clone $detailsQuery)->where('loi_van', true)->count();
        $thanhDieu = (clone $detailsQuery)->where('loi_thanh_dieu', true)->count();
        $totalIncident = max(1, $amDau + $van + $thanhDieu);

        return [
            [
                'type' => 'Lỗi âm đầu',
                'severity' => 'critical',
                'count' => $amDau,
                'percent' => round(($amDau / $totalIncident) * 100),
                'description' => 'Các lỗi nhận diện sai phụ âm đầu.',
            ],
            [
                'type' => 'Lỗi vần',
                'severity' => 'warning',
                'count' => $van,
                'percent' => round(($van / $totalIncident) * 100),
                'description' => 'Các lỗi phát âm phần vần.',
            ],
            [
                'type' => 'Lỗi thanh điệu',
                'severity' => 'info',
                'count' => $thanhDieu,
                'percent' => round(($thanhDieu / $totalIncident) * 100),
                'description' => 'Các lỗi nhầm thanh điệu trong luyện tập.',
            ],
            [
                'type' => 'Phiên luyện tập',
                'severity' => 'info',
                'count' => $totalRequests,
                'percent' => 100,
                'description' => 'Tổng số phiên luyện tập trong khoảng lọc.',
            ],
        ];
    }

    private function buildTopErrors($detailsQuery): array
    {
        $rows = (clone $detailsQuery)
            ->selectRaw("
                SUM(CASE WHEN loi_am_dau = 1 THEN 1 ELSE 0 END) AS am_dau,
                SUM(CASE WHEN loi_van = 1 THEN 1 ELSE 0 END) AS van,
                SUM(CASE WHEN loi_thanh_dieu = 1 THEN 1 ELSE 0 END) AS thanh_dieu
            ")
            ->first();

        return [
            ['id' => 1, 'code' => 'AM_DAU', 'service' => 'Speech Engine', 'count' => (int) ($rows->am_dau ?? 0), 'lastOccurrence' => 'Gần nhất'],
            ['id' => 2, 'code' => 'VAN', 'service' => 'Speech Engine', 'count' => (int) ($rows->van ?? 0), 'lastOccurrence' => 'Gần nhất'],
            ['id' => 3, 'code' => 'THANH_DIEU', 'service' => 'Speech Engine', 'count' => (int) ($rows->thanh_dieu ?? 0), 'lastOccurrence' => 'Gần nhất'],
            ['id' => 4, 'code' => 'USER_LOCKED', 'service' => 'Auth', 'count' => $this->countLockedUsers(), 'lastOccurrence' => 'Hiện tại'],
            ['id' => 5, 'code' => 'LESSON_INACTIVE', 'service' => 'Content', 'count' => BaiHoc::where('trang_thai', 1)->count(), 'lastOccurrence' => 'Hiện tại'],
        ];
    }

    private function countLockedUsers(): int
    {
        if (Schema::hasColumn('nguoi_dungs', 'is_block')) {
            return NguoiDung::where('is_block', 1)->count();
        }
        return NguoiDung::where('trang_thai', 1)->count();
    }

    private function countActiveUsers(): int
    {
        if (Schema::hasColumn('nguoi_dungs', 'is_block')) {
            return NguoiDung::where('is_block', 0)->count();
        }
        return NguoiDung::where('trang_thai', 0)->count();
    }

    private function resolveApiQuotaMetrics(int $todaySessions, int $todayErrorDetails): array
    {
        $defaultLimit = 50000;
        $defaultUsed = min($defaultLimit, (int) ($todaySessions * 13 + $todayErrorDetails * 7 + 5000));

        $record = CauHinhHeThong::query()
            ->where('ma_cau_hinh', 'ai')
            ->first();

        $settings = is_array($record?->du_lieu) ? $record->du_lieu : [];
        $speechToText = is_array($settings['speech_to_text'] ?? null) ? $settings['speech_to_text'] : [];

        $apiLimit = max(0, (int) ($speechToText['monthly_limit'] ?? $defaultLimit));
        $apiUsed = max(0, (int) ($speechToText['current_usage'] ?? $defaultUsed));

        if ($apiLimit > 0) {
            $apiUsed = min($apiUsed, $apiLimit);
        }

        return [$apiUsed, $apiLimit];
    }

    private function getDatabaseRuntimeStats(): array
    {
        try {
            $connectionName = config('database.default');
            $databaseName = config("database.connections.{$connectionName}.database");

            $threadsConnected = DB::selectOne("SHOW GLOBAL STATUS LIKE 'Threads_connected'");
            $slowQueries = DB::selectOne("SHOW GLOBAL STATUS LIKE 'Slow_queries'");
            $questions = DB::selectOne("SHOW GLOBAL STATUS LIKE 'Questions'");

            $sizeRow = DB::selectOne(
                'SELECT SUM(data_length + index_length) AS total_bytes
                 FROM information_schema.tables
                 WHERE table_schema = ?',
                [$databaseName]
            );

            $connected = isset($threadsConnected->Value) ? (int) $threadsConnected->Value : 0;
            $slow = isset($slowQueries->Value) ? (int) $slowQueries->Value : 0;
            $totalQuestions = isset($questions->Value) ? (int) $questions->Value : 0;
            $dbBytes = isset($sizeRow->total_bytes) ? (float) $sizeRow->total_bytes : 0.0;
            $dbSizeGb = $dbBytes / (1024 * 1024 * 1024);

            $optimization = $totalQuestions > 0
                ? max(0, min(100, round(100 - (($slow / $totalQuestions) * 100), 2)))
                : 100;

            return [
                'connections' => $connected,
                'slowQueries' => $slow,
                'optimization' => $optimization,
                'dbSize' => number_format($dbSizeGb, 2) . ' GB',
            ];
        } catch (\Throwable $e) {
            return [
                'connections' => 0,
                'slowQueries' => 0,
                'optimization' => 0,
                'dbSize' => '0.00 GB',
            ];
        }
    }

    private function getLastBackupLabel(): string
    {
        try {
            $metaFile = storage_path('app/backups/last_backup.json');
            if (!File::exists($metaFile)) {
                return 'Chưa backup';
            }

            $raw = File::get($metaFile);
            $json = json_decode($raw, true);
            if (!is_array($json) || empty($json['backup_time_iso'])) {
                return 'Chưa backup';
            }

            $time = Carbon::parse((string) $json['backup_time_iso']);
            return $time->diffForHumans();
        } catch (\Throwable $e) {
            return 'Chưa backup';
        }
    }

    private function getChatSupportStats(): array
    {
        $openSessions = ChatSession::query()
            ->where('status', 'active')
            ->whereNotNull('lesson_id')
            ->count();

        $unreadByTeacher = 0;
        $unreadByStudent = 0;

        if (Schema::hasColumn('chat_messages', 'is_read_by_teacher')) {
            $unreadByTeacher = ChatMessage::query()
                ->where('role', 'user')
                ->where('is_read_by_teacher', false)
                ->count();
        } elseif (Schema::hasColumn('chat_messages', 'is_delivered_to_teacher')) {
            $unreadByTeacher = ChatMessage::query()
                ->where('role', 'user')
                ->where('is_delivered_to_teacher', false)
                ->count();
        }

        if (Schema::hasColumn('chat_messages', 'is_read_by_student')) {
            $unreadByStudent = ChatMessage::query()
                ->where('role', 'teacher')
                ->where('is_read_by_student', false)
                ->count();
        } elseif (Schema::hasColumn('chat_messages', 'is_delivered_to_student')) {
            $unreadByStudent = ChatMessage::query()
                ->where('role', 'teacher')
                ->where('is_delivered_to_student', false)
                ->count();
        }

        return [
            'openSessions' => $openSessions,
            'unreadByTeacher' => $unreadByTeacher,
            'unreadByStudent' => $unreadByStudent,
            'totalPending' => $openSessions + $unreadByTeacher + $unreadByStudent,
        ];
    }
}

