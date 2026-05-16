<?php

namespace App\Http\Controllers;

use App\GoiPremium\GoiPremiumDoiTuong;
use App\Models\GoiNguoiDung;
use App\Models\GoiPremium;
use App\Models\NguoiDung;
use App\Services\GoiPremiumPurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoiPremiumController extends Controller
{
    public function __construct(
        private readonly GoiPremiumPurchaseService $goiPremiumPurchaseService,
    ) {}

    public function goiHienTai(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof NguoiDung) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        $doiTuong = GoiPremiumDoiTuong::tryFromVaiTroId((int) $user->vai_tro_id);
        if ($doiTuong === null) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản không áp dụng gói premium.',
            ], 403);
        }

        $goi = GoiPremium::query()
            ->where('doi_tuong', $doiTuong->value)
            ->where('trang_thai', 1)
            ->first();

        $goiDangDung = GoiNguoiDung::query()
            ->where('nguoi_dung_id', $user->id)
            ->where('trang_thai', GoiNguoiDung::TRANG_THAI_HOAT_DONG)
            ->where('ngay_het_han', '>', now())
            ->with('goiPremium')
            ->orderByDesc('ngay_het_han')
            ->first();

        $coTheMua = $goi !== null && $goiDangDung === null;

        return response()->json([
            'status' => true,
            'goi' => $goi ? $this->serializeGoi($goi) : null,
            'goi_dang_dung' => $goiDangDung ? $this->serializeGoiNguoiDung($goiDangDung) : null,
            'co_the_mua' => $coTheMua,
        ]);
    }

    public function mua(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof NguoiDung) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        try {
            $result = $this->goiPremiumPurchaseService->mua($user);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => false,
                'message' => match ($e->getMessage()) {
                    'INVALID_ROLE' => 'Tài khoản không được mua gói premium.',
                    'NO_PACKAGE' => 'Hiện không có gói premium đang mở bán cho vai trò của bạn.',
                    'ALREADY_ACTIVE' => 'Bạn đang có gói premium còn hiệu lực. Hết hạn mới có thể mua lại.',
                    'INSUFFICIENT_BALANCE' => 'Số dư ví không đủ để mua gói.',
                    default => 'Không thể hoàn tất giao dịch.',
                },
            ], 422);
        }

        $gn = $result['goi_nguoi_dung'];

        return response()->json([
            'status' => true,
            'message' => 'Mua gói premium thành công.',
            'so_du_sau' => $result['so_du_sau'],
            'goi_nguoi_dung' => $gn ? $this->serializeGoiNguoiDung($gn) : null,
        ]);
    }

    private function serializeGoi(GoiPremium $goi): array
    {
        return [
            'id' => $goi->id,
            'ten_goi' => $goi->ten_goi,
            'doi_tuong' => $goi->doi_tuong,
            'mo_ta' => $goi->mo_ta,
            'gia' => (int) $goi->gia,
            'thoi_han_ngay' => (int) $goi->thoi_han_ngay,
            'tinh_nang' => $goi->tinh_nang,
            'trang_thai' => (int) $goi->trang_thai,
        ];
    }

    private function serializeGoiNguoiDung(GoiNguoiDung $row): array
    {
        $goi = $row->relationLoaded('goiPremium') ? $row->goiPremium : $row->goiPremium()->first();

        return [
            'id' => $row->id,
            'goi_id' => $row->goi_id,
            'gia_da_mua' => (int) $row->gia_da_mua,
            'ngay_kich_hoat' => $row->ngay_kich_hoat?->toIso8601String(),
            'ngay_het_han' => $row->ngay_het_han?->toIso8601String(),
            'trang_thai' => (int) $row->trang_thai,
            'goi' => $goi ? $this->serializeGoi($goi) : null,
        ];
    }
}
