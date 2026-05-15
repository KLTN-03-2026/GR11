<?php

namespace App\Http\Controllers;

use App\Models\AiReportSnapshot;
use App\Models\NguoiDung;
use App\Services\AI\Rag\Reports\ReportSnapshotService;
use App\Services\AI\Rag\Support\PremiumFeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiReportController extends Controller
{
    public function __construct(
        private readonly ReportSnapshotService $reportSnapshotService,
        private readonly PremiumFeatureService $premiumFeatureService,
    ) {}

    public function latest(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof NguoiDung) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        $gate = $this->premiumGate($user);
        if ($gate !== null) {
            return $gate;
        }

        $monthsBack = max(0, (int) $request->query('months_back', 0));
        $data = $this->loadSnapshot($user, $monthsBack);

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function exportCsv(Request $request, int $snapshotId): JsonResponse
    {
        return $this->exportFile($request, $snapshotId, 'csv');
    }

    public function exportPdf(Request $request, int $snapshotId): JsonResponse
    {
        return $this->exportFile($request, $snapshotId, 'pdf');
    }

    public function regenerate(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof NguoiDung) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        $gate = $this->premiumGate($user);
        if ($gate !== null) {
            return $gate;
        }

        $monthsBack = max(0, (int) $request->input('months_back', 0));
        $from = now()->startOfMonth()->subMonths($monthsBack);

        AiReportSnapshot::query()
            ->where('nguoi_dung_id', $user->id)
            ->whereDate('tu_ngay', $from->toDateString())
            ->delete();

        $data = $this->loadSnapshot($user, $monthsBack);

        return response()->json(['status' => true, 'data' => $data]);
    }

    private function exportFile(Request $request, int $snapshotId, string $type): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof NguoiDung) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        $snapshot = AiReportSnapshot::query()
            ->where('id', $snapshotId)
            ->where('nguoi_dung_id', $user->id)
            ->first();

        if ($snapshot === null) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy báo cáo.'], 404);
        }

        try {
            $url = $type === 'pdf'
                ? $this->reportSnapshotService->exportPdf($snapshot)
                : $this->reportSnapshotService->exportCsv($snapshot);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không tạo được file báo cáo. Vui lòng thử lại sau.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'download_url' => $url,
            'file_type' => $type,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function loadSnapshot(NguoiDung $user, int $monthsBack): array
    {
        return (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER
            ? $this->reportSnapshotService->getOrCreateTeacherMonthly($user, $monthsBack)
            : $this->reportSnapshotService->getOrCreateStudentMonthly($user, $monthsBack);
    }

    private function premiumGate(NguoiDung $user): ?JsonResponse
    {
        $feature = (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER
            ? PremiumFeatureService::FEATURE_CLASS_REPORT
            : PremiumFeatureService::FEATURE_MONTHLY_REPORT;

        $gate = $this->premiumFeatureService->gate($user, $feature);
        if ($gate['allowed']) {
            return null;
        }

        return response()->json([
            'status' => false,
            'premium_required' => true,
            'message' => $gate['upsell_message'],
            'action_url' => $gate['action_url'],
        ], 403);
    }
}
