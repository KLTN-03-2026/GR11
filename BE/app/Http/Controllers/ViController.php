<?php

namespace App\Http\Controllers;

use App\Models\DonNapTien;
use App\Models\GiaoDichVi;
use App\Models\Vi;
use App\Models\YeuCauRutTien;
use App\Services\DepositDonService;
use App\Services\GoiPremiumPurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViController extends Controller
{
    public function __construct(
        private readonly DepositDonService $depositDonService,
    ) {}

    private function viCuaNguoiDung(int $nguoiDungId): Vi
    {
        return Vi::query()->firstOrCreate(
            ['nguoi_dung_id' => $nguoiDungId],
            ['so_du' => 0],
        );
    }

    public function soDu(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $vi = $this->viCuaNguoiDung((int) $user->id);

        return response()->json([
            'status' => true,
            'so_du' => (int) $vi->so_du,
        ]);
    }

    public function napTien(Request $request): JsonResponse
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

        $this->viCuaNguoiDung((int) $user->id);

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

    public function napTienTrangThai(Request $request, string $maDon): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $don = DonNapTien::query()
            ->where('ma_don', $maDon)
            ->where('nguoi_dung_id', $user->id)
            ->first();

        if (! $don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn nạp.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'trang_thai' => $don->trang_thai,
            'ma_don' => $don->ma_don,
        ]);
    }

    /**
     * @deprecated Luồng nạp do admin xác nhận.
     */
    public function napTienSauQuetMa(Request $request, string $maDon): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Đơn nạp được xác nhận bởi quản trị viên sau khi nhận được chuyển khoản.',
        ], 422);
    }

    /**
     * @deprecated Chỉ admin xác nhận qua API quản trị.
     */
    public function napTienXacNhanThanhCong(Request $request, string $maDon): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Vui lòng chờ quản trị viên xác nhận sau khi bạn đã chuyển khoản.',
        ], 403);
    }

    public function rutTien(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $validated = $request->validate([
            'so_tien' => ['required', 'integer', 'min:50000'],
            'ten_ngan_hang' => ['required', 'string', 'max:100'],
            'so_tai_khoan' => ['required', 'string', 'max:30'],
            'chu_tai_khoan' => ['required', 'string', 'max:100'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ]);

        $vi = $this->viCuaNguoiDung((int) $user->id);

        if ($validated['so_tien'] > (int) $vi->so_du) {
            return response()->json([
                'status' => false,
                'message' => 'Số tiền rút vượt quá số dư trong ví.',
            ], 422);
        }

        YeuCauRutTien::query()->create([
            'giao_vien_id' => $user->id,
            'so_tien' => $validated['so_tien'],
            'ten_ngan_hang_snapshot' => $validated['ten_ngan_hang'],
            'so_tai_khoan_snapshot' => $validated['so_tai_khoan'],
            'chu_tai_khoan_snapshot' => $validated['chu_tai_khoan'],
            'trang_thai' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Yêu cầu rút tiền đã được ghi nhận!',
        ]);
    }

    public function lichSu(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $loai = $request->query('loai');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 15;

        $naps = DonNapTien::query()
            ->where('nguoi_dung_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (DonNapTien $d) {
                return [
                    '_sort' => $d->created_at?->timestamp ?? 0,
                    'id' => 'nap-'.$d->id,
                    'loai' => 'nap',
                    'mo_ta' => 'Đơn nạp '.$d->ma_don,
                    'created_at' => $d->created_at?->format('d/m/Y H:i') ?? '',
                    'so_tien' => (int) $d->so_tien,
                    'trang_thai' => $d->trang_thai,
                ];
            });

        $ruts = YeuCauRutTien::query()
            ->where('giao_vien_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (YeuCauRutTien $r) {
                $tt = match ((int) $r->trang_thai) {
                    1 => 'thanh_cong',
                    2 => 'that_bai',
                    default => 'cho_xu_ly',
                };

                return [
                    '_sort' => $r->created_at?->timestamp ?? 0,
                    'id' => 'rut-'.$r->id,
                    'loai' => 'rut',
                    'mo_ta' => 'Rút tiền về '.$r->ten_ngan_hang_snapshot,
                    'created_at' => $r->created_at?->format('d/m/Y H:i') ?? '',
                    'so_tien' => (int) $r->so_tien,
                    'trang_thai' => $tt,
                ];
            });

        $vi = $this->viCuaNguoiDung((int) $user->id);
        $muaGoi = GiaoDichVi::query()
            ->where('vi_id', $vi->id)
            ->where('loai_giao_dich', GoiPremiumPurchaseService::LOAI_GIAO_DICH)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (GiaoDichVi $g) {
                return [
                    '_sort' => $g->created_at?->timestamp ?? 0,
                    'id' => 'gd-'.$g->id,
                    'loai' => 'mua_goi',
                    'mo_ta' => $g->ghi_chu ?? 'Mua gói premium',
                    'created_at' => $g->created_at?->format('d/m/Y H:i') ?? '',
                    'so_tien' => (int) $g->so_tien,
                    'trang_thai' => 'thanh_cong',
                ];
            });

        $merged = $naps->concat($ruts)->concat($muaGoi);

        if ($loai === 'nap') {
            $merged = $merged->where('loai', 'nap')->values();
        } elseif ($loai === 'rut') {
            $merged = $merged->where('loai', 'rut')->values();
        } elseif ($loai === 'mua_goi') {
            $merged = $merged->where('loai', 'mua_goi')->values();
        } elseif (in_array($loai, ['thanh_toan', 'hoan_tien'], true)) {
            $merged = collect();
        }

        $sorted = $merged->sortByDesc('_sort')->values()->map(function (array $row) {
            unset($row['_sort']);

            return $row;
        });

        $total = $sorted->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $slice = $sorted->forPage($page, $perPage)->values();

        return response()->json([
            'status' => true,
            'data' => $slice,
            'current_page' => $page,
            'last_page' => $lastPage,
        ]);
    }
}
