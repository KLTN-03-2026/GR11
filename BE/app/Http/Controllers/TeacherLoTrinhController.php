<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\ChiTietLoTrinh;
use App\Models\LoTrinhCaNhan;
use App\Models\LoTrinhTraPhi;
use App\Models\QuanHeGvHv;
use App\Models\QuyenLoTrinh;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherLoTrinhController extends Controller
{
    private function laGiaoVienPhuTrach(int $giaoVienId, int $hocVienId): bool
    {
        return QuanHeGvHv::query()
            ->where('giao_vien_id', $giaoVienId)
            ->where('hoc_vien_id', $hocVienId)
            ->exists();
    }

    private function loTrinhCuaGiaoVien(LoTrinhCaNhan $loTrinh, int $giaoVienId): bool
    {
        return (int) $loTrinh->giao_vien_id === $giaoVienId;
    }

    public function index(Request $request): JsonResponse
    {
        $gv = $request->user();
        $hocVienId = $request->query('hoc_vien_id');
        $q = LoTrinhCaNhan::query()
            ->where('giao_vien_id', $gv->id)
            ->with(['traPhi'])
            ->orderByDesc('id');

        if ($hocVienId !== null && $hocVienId !== '') {
            $hvId = (int) $hocVienId;
            if (! $this->laGiaoVienPhuTrach((int) $gv->id, $hvId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Học viên không thuộc danh sách phụ trách của bạn.',
                ], 403);
            }
            $q->where('hoc_vien_id', $hvId);
        }

        $items = $q->get();

        $quyenByLoTrinhId = $items->isEmpty()
            ? collect()
            : QuyenLoTrinh::query()
                ->whereIn('lo_trinh_id', $items->pluck('id'))
                ->get()
                ->keyBy('lo_trinh_id');

        $data = $items->map(function (LoTrinhCaNhan $lt) use ($quyenByLoTrinhId): array {
            $tp = $lt->traPhi;
            $gia = $tp ? (int) $tp->gia : 0;

            return array_merge([
                'id' => $lt->id,
                'hoc_vien_id' => $lt->hoc_vien_id,
                'ten_lo_trinh' => $lt->ten_lo_trinh,
                'tra_phi' => $tp ? [
                    'gia' => $gia,
                    'mo_ta_ban' => $tp->mo_ta_ban,
                    'trang_thai' => (int) $tp->trang_thai,
                ] : null,
                'la_tra_phi' => $gia > 0 && $tp && (int) $tp->trang_thai === LoTrinhTraPhi::TRANG_THAI_DA_DUYET,
            ], $this->thongKeThanhToanHocVien($lt->id, (int) $lt->hoc_vien_id, $tp, $quyenByLoTrinhId->get($lt->id)));
        });

        return response()->json([
            'status' => true,
            'data' => $data->values(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function thongKeThanhToanHocVien(int $loTrinhId, int $hocVienId, ?LoTrinhTraPhi $tp, ?QuyenLoTrinh $quyen): array
    {
        $gia = $tp ? (int) $tp->gia : 0;
        $laTraPhi = $gia > 0 && $tp && (int) $tp->trang_thai === LoTrinhTraPhi::TRANG_THAI_DA_DUYET;

        if ($quyen !== null && ((int) $quyen->hoc_vien_id !== $hocVienId || (int) $quyen->lo_trinh_id !== $loTrinhId)) {
            $quyen = null;
        }

        if (! $laTraPhi) {
            return [
                'da_mua' => false,
                'gia_hoc_vien_da_tra' => null,
                'thu_nhap_gv_sau_khau_tru' => null,
                'phi_nen_tang' => null,
                'ti_le_hoa_hong_platform' => null,
                'ngay_mua' => null,
            ];
        }

        $daMua = $quyen !== null;

        return [
            'da_mua' => $daMua,
            'gia_hoc_vien_da_tra' => $daMua ? (int) $quyen->gia_da_mua : null,
            'thu_nhap_gv_sau_khau_tru' => $daMua ? (int) $quyen->so_tien_giao_vien_nhan : null,
            'phi_nen_tang' => $daMua ? max(0, (int) $quyen->gia_da_mua - (int) $quyen->so_tien_giao_vien_nhan) : null,
            'ti_le_hoa_hong_platform' => $daMua ? (float) $quyen->ti_le_hoa_hong_da_ap : null,
            'ngay_mua' => $daMua && $quyen->ngay_mua ? $quyen->ngay_mua->format('c') : null,
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $gv = $request->user();
        $validated = $request->validate([
            'hoc_vien_id' => ['required', 'integer', 'exists:nguoi_dungs,id'],
            'ten_lo_trinh' => ['required', 'string', 'max:255'],
        ]);

        if (! $this->laGiaoVienPhuTrach((int) $gv->id, (int) $validated['hoc_vien_id'])) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không phụ trách học viên này.',
            ], 403);
        }

        $lt = LoTrinhCaNhan::query()->create([
            'hoc_vien_id' => (int) $validated['hoc_vien_id'],
            'giao_vien_id' => (int) $gv->id,
            'ten_lo_trinh' => $validated['ten_lo_trinh'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã tạo lộ trình.',
            'data' => ['id' => $lt->id],
        ], 201);
    }

    public function show(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $gv = $request->user();
        if (! $this->loTrinhCuaGiaoVien($loTrinh, (int) $gv->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lộ trình.',
            ], 404);
        }

        $loTrinh->load([
            'chiTiet.baiHoc:id,tieu_de,danh_muc_id',
            'traPhi',
        ]);

        $chiTiet = $loTrinh->chiTiet->map(function (ChiTietLoTrinh $ct) {
            return [
                'bai_hoc_id' => $ct->bai_hoc_id,
                'tieu_de' => $ct->baiHoc?->tieu_de,
                'thu_tu_uu_tien' => (int) $ct->thu_tu_uu_tien,
                'ghi_chu_gv' => $ct->ghi_chu_gv,
            ];
        })->values();

        $tp = $loTrinh->traPhi;
        $quyen = QuyenLoTrinh::query()
            ->where('lo_trinh_id', $loTrinh->id)
            ->where('hoc_vien_id', $loTrinh->hoc_vien_id)
            ->first();

        $giaTp = $tp ? (int) $tp->gia : 0;
        $laTraPhi = $giaTp > 0 && $tp && (int) $tp->trang_thai === LoTrinhTraPhi::TRANG_THAI_DA_DUYET;

        return response()->json([
            'status' => true,
            'data' => array_merge([
                'id' => $loTrinh->id,
                'hoc_vien_id' => $loTrinh->hoc_vien_id,
                'ten_lo_trinh' => $loTrinh->ten_lo_trinh,
                'chi_tiet' => $chiTiet,
                'tra_phi' => $tp ? [
                    'gia' => $giaTp,
                    'mo_ta_ban' => $tp->mo_ta_ban,
                    'trang_thai' => (int) $tp->trang_thai,
                ] : null,
                'la_tra_phi' => $laTraPhi,
            ], $this->thongKeThanhToanHocVien((int) $loTrinh->id, (int) $loTrinh->hoc_vien_id, $tp, $quyen)),
        ]);
    }

    public function update(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $gv = $request->user();
        if (! $this->loTrinhCuaGiaoVien($loTrinh, (int) $gv->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lộ trình.',
            ], 404);
        }

        $validated = $request->validate([
            'ten_lo_trinh' => ['required', 'string', 'max:255'],
        ]);

        $loTrinh->update(['ten_lo_trinh' => $validated['ten_lo_trinh']]);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật lộ trình.',
        ]);
    }

    public function destroy(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $gv = $request->user();
        if (! $this->loTrinhCuaGiaoVien($loTrinh, (int) $gv->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lộ trình.',
            ], 404);
        }

        $loTrinh->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa lộ trình.',
        ]);
    }

    public function syncChiTiet(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $gv = $request->user();
        if (! $this->loTrinhCuaGiaoVien($loTrinh, (int) $gv->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lộ trình.',
            ], 404);
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.bai_hoc_id' => ['required', 'integer', 'exists:bai_hocs,id'],
            'items.*.thu_tu_uu_tien' => ['required', 'integer', 'min:1'],
            'items.*.ghi_chu_gv' => ['nullable', 'string', 'max:5000'],
        ]);

        $baiHocIds = collect($validated['items'])->pluck('bai_hoc_id')->unique()->values();
        $hopLe = BaiHoc::query()
            ->whereIn('id', $baiHocIds)
            ->where('nguoi_tao_id', $gv->id)
            ->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)
            ->pluck('id');

        if ($hopLe->count() !== $baiHocIds->count()) {
            return response()->json([
                'status' => false,
                'message' => 'Một hoặc nhiều bài học không thuộc bạn hoặc chưa hoạt động.',
            ], 422);
        }

        DB::transaction(function () use ($loTrinh, $validated): void {
            ChiTietLoTrinh::query()->where('lo_trinh_id', $loTrinh->id)->delete();
            foreach ($validated['items'] as $row) {
                ChiTietLoTrinh::query()->create([
                    'lo_trinh_id' => $loTrinh->id,
                    'bai_hoc_id' => (int) $row['bai_hoc_id'],
                    'thu_tu_uu_tien' => (int) $row['thu_tu_uu_tien'],
                    'ghi_chu_gv' => $row['ghi_chu_gv'] ?? null,
                ]);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu thứ tự và chi tiết lộ trình.',
        ]);
    }

    public function upsertTraPhi(Request $request, LoTrinhCaNhan $loTrinh): JsonResponse
    {
        $gv = $request->user();
        if (! $this->loTrinhCuaGiaoVien($loTrinh, (int) $gv->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lộ trình.',
            ], 404);
        }

        $validated = $request->validate([
            'gia' => ['required', 'integer', 'min:0'],
            'mo_ta_ban' => ['nullable', 'string', 'max:5000'],
        ]);

        $gia = (int) $validated['gia'];
        $now = now();

        if ($gia === 0) {
            LoTrinhTraPhi::query()->where('lo_trinh_id', $loTrinh->id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Lộ trình hiện miễn phí (đã gỡ cấu hình phí).',
            ]);
        }

        LoTrinhTraPhi::query()->updateOrCreate(
            ['lo_trinh_id' => $loTrinh->id],
            [
                'gia' => $gia,
                'mo_ta_ban' => $validated['mo_ta_ban'] ?? null,
                'trang_thai' => LoTrinhTraPhi::TRANG_THAI_DA_DUYET,
                'ngay_duyet' => $now,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu giá lộ trình.',
        ]);
    }
}
