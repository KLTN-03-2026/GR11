<?php

namespace App\Http\Controllers;

use App\Services\DepositDonService;
use App\Models\DonNapTien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(
        private readonly DepositDonService $depositDonService,
    ) {}

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $validated = $request->validate([
            'so_tien' => ['required', 'integer', 'min:10000'],
            'phuong_thuc' => ['nullable', 'string', 'max:50'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ]);

        $created = $this->depositDonService->create(
            $user,
            (int) $validated['so_tien'],
            $validated['phuong_thuc'] ?? null,
            $validated['ghi_chu'] ?? null,
        );

        $don = $created['don'];

        return response()->json([
            'status' => true,
            'message' => 'Đơn nạp đã tạo. Quét mã QR để chuyển khoản (ghi đúng mã đơn trong nội dung CK).',
            'ma_don' => $don->ma_don,
            'so_tien' => (int) $don->so_tien,
            'trang_thai' => $don->trang_thai,
            'qr_url' => $created['qr_url'],
            'vietqr_configured' => $created['vietqr_configured'],
            'qr_account_name' => $created['qr_account_name'] ?: null,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $perPage = min(50, max(5, (int) $request->query('per_page', 15)));

        $paginator = DonNapTien::query()
            ->where('nguoi_dung_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $data = $paginator->getCollection()->map(function (DonNapTien $d) {
            return [
                'id' => $d->id,
                'ma_don' => $d->ma_don,
                'so_tien' => (int) $d->so_tien,
                'trang_thai' => $d->trang_thai,
                'created_at' => $d->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]);
    }
}
