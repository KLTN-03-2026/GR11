<?php

namespace App\Http\Controllers;

use App\Models\ThongTinHocVien;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TienDoBaiHocController extends Controller
{
    /**
     * Lấy toàn bộ dữ liệu tiến độ học tập của người dùng
     */
    public function tongQuan(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = (int) $user->id;

        $thongTin = ThongTinHocVien::where('nguoi_dung_id', $userId)->first();

        $soBaiHoc = TienDoBaiHoc::where('hoc_vien_id', $userId)
            ->where('so_tu_da_hoc', '>', 0)
            ->count();

        $phanTramPhatAm = (float) DB::table('chi_tiet_luyen_taps')
            ->join('phien_luyen_taps', 'phien_luyen_taps.id', '=', 'chi_tiet_luyen_taps.phien_id')
            ->where('phien_luyen_taps.nguoi_dung_id', $userId)
            ->whereNotNull('chi_tiet_luyen_taps.diem_chinh_xac')
            ->avg('chi_tiet_luyen_taps.diem_chinh_xac') ?? 0;

        $baiGanNhat = TienDoBaiHoc::where('hoc_vien_id', $userId)
            ->whereNotNull('thoi_gian_hoc_cuoi')
            ->orderByDesc('thoi_gian_hoc_cuoi')
            ->with('baiHoc:id,tieu_de')
            ->first();

        // Tiến độ lộ trình (giới hạn 3)
        $limit = (int) $request->input('lo_trinh_limit', 3);
        $offset = (int) $request->input('lo_trinh_offset', 0);

        $loTrinhs = DB::table('lo_trinh_ca_nhans')
            ->where('hoc_vien_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $tongLoTrinh = $loTrinhs->count();
        $loTrinhPage = $loTrinhs->slice($offset, $limit)->values();

        $loTrinhData = $loTrinhPage->map(function ($lt) use ($userId) {
            $baiHocIds = DB::table('chi_tiet_lo_trinhs')
                ->where('lo_trinh_id', $lt->id)
                ->pluck('bai_hoc_id');

            $tongTuVung = DB::table('tu_vungs')
                ->whereIn('bai_hoc_id', $baiHocIds)
                ->count();

            $soTuDaHoc = 0;
            if ($tongTuVung > 0) {
                $phienIds = DB::table('phien_luyen_taps')
                    ->where('nguoi_dung_id', $userId)
                    ->whereIn('bai_hoc_id', $baiHocIds)
                    ->pluck('id');

                $soTuDaHoc = DB::table('chi_tiet_luyen_taps')
                    ->whereIn('phien_id', $phienIds)
                    ->distinct()
                    ->count('tu_vung_id');
            }

            $phanTram = $tongTuVung > 0
                ? min(100, round(($soTuDaHoc / $tongTuVung) * 100, 2))
                : 0;

            return [
                'id' => $lt->id,
                'ten_lo_trinh' => $lt->ten_lo_trinh,
                'tien_do' => $phanTram,
                'tong_tu_vung' => $tongTuVung,
                'so_tu_da_hoc' => $soTuDaHoc,
            ];
        });

        // Tiến độ bài học (giới hạn 5)
        $baiHocLimit = (int) $request->input('bai_hoc_limit', 5);
        $baiHocOffset = (int) $request->input('bai_hoc_offset', 0);

        $tongBaiHocTienDo = TienDoBaiHoc::where('hoc_vien_id', $userId)->count();

        $dsBaiHoc = TienDoBaiHoc::where('hoc_vien_id', $userId)
            ->orderByDesc('thoi_gian_hoc_cuoi')
            ->with('baiHoc:id,tieu_de')
            ->skip($baiHocOffset)
            ->take($baiHocLimit)
            ->get()
            ->map(function ($td) {
                $tongTuVung = DB::table('tu_vungs')
                    ->where('bai_hoc_id', $td->bai_hoc_id)
                    ->count();

                $phanTram = $tongTuVung > 0
                    ? min(100, round(($td->so_tu_da_hoc / $tongTuVung) * 100, 2))
                    : 0;

                return [
                    'bai_hoc_id' => $td->bai_hoc_id,
                    'tieu_de' => $td->baiHoc->tieu_de ?? null,
                    'so_tu_da_hoc' => $td->so_tu_da_hoc,
                    'tong_tu_vung' => $tongTuVung,
                    'tien_do' => $phanTram,
                    'diem_trung_binh' => $td->diem_trung_binh,
                    'thoi_gian_hoc_cuoi' => $td->thoi_gian_hoc_cuoi,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'so_bai_hoc' => $soBaiHoc,
                'phan_tram_phat_am' => round($phanTramPhatAm, 2),
                'streak_hien_tai' => (int) ($thongTin->streak_hien_tai ?? 0),
                'diem_tich_luy' => (int) ($thongTin->diem_tich_luy ?? 0),
                'bai_gan_nhat' => $baiGanNhat ? [
                    'bai_hoc_id' => $baiGanNhat->bai_hoc_id,
                    'tieu_de' => $baiGanNhat->baiHoc->tieu_de ?? null,
                    'thoi_gian_hoc_cuoi' => $baiGanNhat->thoi_gian_hoc_cuoi,
                ] : null,
                'lo_trinhs' => [
                    'danh_sach' => $loTrinhData,
                    'tong' => $tongLoTrinh,
                    'con_lai' => max(0, $tongLoTrinh - $offset - $limit),
                ],
                'bai_hocs' => [
                    'danh_sach' => $dsBaiHoc,
                    'tong' => $tongBaiHocTienDo,
                    'con_lai' => max(0, $tongBaiHocTienDo - $baiHocOffset - $baiHocLimit),
                ],
            ],
        ]);
    }
}
