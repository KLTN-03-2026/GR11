<?php

namespace App\Http\Controllers;

use App\Models\LoTrinhCaNhan;
use App\Models\LoTrinhTraPhi;
use App\Models\NguoiDung;
use App\Models\QuyenLoTrinh;
use App\Services\LoTrinhPurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HocVienLoTrinhController extends Controller
{
    public function __construct(
        private readonly LoTrinhPurchaseService $loTrinhPurchaseService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user || (int) $user->vai_tro_id !== NguoiDung::ROLE_USER) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ học viên mới xem được danh sách này.',
            ], 403);
        }

        $userId = (int) $user->id;
        $rows = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', $userId)
            ->with(['traPhi'])
            ->orderByDesc('id')
            ->get();

        $data = $rows->map(function (LoTrinhCaNhan $lt) use ($userId) {
            $tp = $lt->traPhi;
            $gia = $tp ? (int) $tp->gia : 0;
            $duyet = $tp && (int) $tp->trang_thai === LoTrinhTraPhi::TRANG_THAI_DA_DUYET;
            $laTraPhi = $gia > 0 && $duyet;
            $daMua = $laTraPhi && QuyenLoTrinh::query()
                ->where('lo_trinh_id', $lt->id)
                ->where('hoc_vien_id', $userId)
                ->exists();

            return [
                'id' => $lt->id,
                'ten_lo_trinh' => $lt->ten_lo_trinh,
                'gia' => $gia,
                'tra_phi_da_duyet' => $duyet,
                'la_tra_phi' => $laTraPhi,
                'da_mua' => $daMua,
                'can_hoc' => ! $laTraPhi || $daMua,
                'can_mua' => $laTraPhi && ! $daMua,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function show(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $user = $request->user();
        if (! $user || (int) $user->vai_tro_id !== NguoiDung::ROLE_USER) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ học viên mới xem được.',
            ], 403);
        }

        if ((int) $loTrinh->hoc_vien_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lộ trình.',
            ], 404);
        }

        $tp = $loTrinh->traPhi()->first();
        $gia = $tp ? (int) $tp->gia : 0;
        $duyet = $tp && (int) $tp->trang_thai === LoTrinhTraPhi::TRANG_THAI_DA_DUYET;
        $laTraPhi = $gia > 0 && $duyet;
        $daMua = QuyenLoTrinh::query()
            ->where('lo_trinh_id', $loTrinh->id)
            ->where('hoc_vien_id', $user->id)
            ->exists();

        if ($laTraPhi && ! $daMua) {
            return response()->json([
                'status' => false,
                'message' => 'Lộ trình trả phí. Vui lòng đăng ký thanh toán để xem nội dung.',
                'code' => 'REQUIRES_PURCHASE',
                'data' => [
                    'lo_trinh_id' => $loTrinh->id,
                    'gia' => $gia,
                    'mo_ta_ban' => $tp?->mo_ta_ban,
                ],
            ], 403);
        }

        $chiTiet = $loTrinh->chiTiet()
            ->with(['baiHoc:id,tieu_de'])
            ->orderBy('thu_tu_uu_tien')
            ->get()
            ->map(fn ($ct) => [
                'bai_hoc_id' => $ct->bai_hoc_id,
                'tieu_de' => $ct->baiHoc?->tieu_de,
                'thu_tu_uu_tien' => (int) $ct->thu_tu_uu_tien,
                'ghi_chu_gv' => $ct->ghi_chu_gv,
            ]);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $loTrinh->id,
                'ten_lo_trinh' => $loTrinh->ten_lo_trinh,
                'chi_tiet' => $chiTiet,
                'tra_phi' => $tp ? [
                    'gia' => $gia,
                    'mo_ta_ban' => $tp->mo_ta_ban,
                ] : null,
                'da_mua' => $daMua,
            ],
        ]);
    }

    public function mua(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $user = $request->user();
        if (! $user || (int) $user->vai_tro_id !== NguoiDung::ROLE_USER) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ học viên mới thực hiện được.',
            ], 403);
        }

        try {
            $result = $this->loTrinhPurchaseService->mua($user, $loTrinh);
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();
            $payload = [
                'status' => false,
                'code' => $code,
                'message' => match ($code) {
                    'INSUFFICIENT_BALANCE' => 'Số dư ví không đủ. Vui lòng nạp tiền (Ví hoặc đơn nạp) rồi thử lại.',
                    'NOT_OWNER' => 'Lộ trình không thuộc tài khoản của bạn.',
                    'NOT_PAID_ROADMAP' => 'Lộ trình không yêu cầu thanh toán hoặc chưa được duyệt giá.',
                    'ALREADY_PURCHASED' => 'Bạn đã đăng ký lộ trình này.',
                    default => 'Không thể hoàn tất giao dịch.',
                },
            ];
            if ($code === 'INSUFFICIENT_BALANCE') {
                $payload['huong_dan'] = [
                    'nap_tien_vi' => '/profile',
                    'api_nap_tien' => '/api/vi/nap-tien',
                    'api_deposit' => '/api/deposit/create',
                ];
            }

            $status = $code === 'INSUFFICIENT_BALANCE' ? 422 : 400;

            return response()->json($payload, $status);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã thanh toán và mở quyền học lộ trình.',
            'data' => [
                'so_du_hoc_vien_sau' => $result['so_du_sau'],
                'quyen_id' => $result['quyen']->id,
                'gia' => $result['quyen']->gia_da_mua,
                'ti_le_hoa_hong_platform_percent' => $result['quyen']->ti_le_hoa_hong_da_ap,
                'tien_giao_vien_nhan' => $result['tien_giao_vien_nhan'],
                'so_du_giao_vien_sau' => $result['so_du_giao_vien_sau'],
                'tien_phi_nen_tang_giu_lai' => $result['tien_phi_nen_tang'],
            ],
        ]);
    }
}
