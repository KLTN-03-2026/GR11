<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TrangChuController extends Controller
{
    public function index(): JsonResponse
    {
        // Chủ đề nổi bật: dựa theo số phiên luyện thuộc các bài học trong danh mục (fallback: số bài học)
        $chuDeNoiBat = DanhMucBaiHoc::query()
            ->where('trang_thai', 1)
            ->withCount([
                'baiHocs as so_luong_bai_hoc' => function ($q): void {
                    $q->where('trang_thai', 1);
                },
            ])
            ->leftJoin('bai_hocs', function ($join): void {
                $join->on('bai_hocs.danh_muc_id', '=', 'danh_muc_bai_hocs.id')
                    ->where('bai_hocs.trang_thai', 1);
            })
            ->leftJoin('phien_luyen_taps', 'phien_luyen_taps.bai_hoc_id', '=', 'bai_hocs.id')
            ->groupBy('danh_muc_bai_hocs.id')
            ->orderByDesc(DB::raw('COUNT(phien_luyen_taps.id)'))
            ->orderByDesc('so_luong_bai_hoc')
            ->orderBy('danh_muc_bai_hocs.thu_tu')
            ->limit(6)
            ->get([
                'danh_muc_bai_hocs.id',
                'danh_muc_bai_hocs.ten_danh_muc',
                'danh_muc_bai_hocs.slug_danh_muc',
                'danh_muc_bai_hocs.mo_ta',
                'danh_muc_bai_hocs.thu_tu',
                DB::raw('COUNT(phien_luyen_taps.id) as luot_luyen'),
            ]);

        // Bài học nổi bật: dựa theo số phiên luyện (fallback: bài mới)
        $baiHocNoiBat = BaiHoc::query()
            ->where('trang_thai', 1)
            ->with([
                'danhMuc:id,ten_danh_muc,slug_danh_muc',
            ])
            ->withCount([
                'tuVungs as so_tu_vung',
            ])
            ->leftJoin('phien_luyen_taps', 'phien_luyen_taps.bai_hoc_id', '=', 'bai_hocs.id')
            ->groupBy('bai_hocs.id')
            ->orderByDesc(DB::raw('COUNT(phien_luyen_taps.id)'))
            ->orderByDesc('bai_hocs.id')
            ->limit(8)
            ->get([
                'bai_hocs.*',
                DB::raw('COUNT(phien_luyen_taps.id) as luot_luyen'),
            ])
            ->values();

        // Học sinh nổi bật: dựa theo điểm trung bình & số phiên luyện (chỉ lấy vai trò học viên)
        $hocSinhNoiBat = PhienLuyenTap::query()
            ->select(
                'nguoi_dung_id',
                DB::raw('COUNT(*) as so_phien'),
                DB::raw('AVG(COALESCE(tong_diem, 0)) as diem_tb')
            )
            ->groupBy('nguoi_dung_id')
            ->orderByDesc('diem_tb')
            ->orderByDesc('so_phien')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $u = NguoiDung::query()
                    ->where('id', $row->nguoi_dung_id)
                    ->where('vai_tro_id', NguoiDung::ROLE_USER)
                    ->first(['id', 'ho_ten', 'anh_dai_dien']);

                if (! $u) {
                    return null;
                }

                return [
                    'id' => $u->id,
                    'ho_ten' => $u->ho_ten,
                    'anh_dai_dien' => $u->anh_dai_dien,
                    'so_phien' => (int) $row->so_phien,
                    'diem_trung_binh' => round((float) $row->diem_tb, 1),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'status' => true,
            'data' => [
                'chu_de_noi_bat' => $chuDeNoiBat,
                'bai_hoc_noi_bat' => $baiHocNoiBat,
                'hoc_sinh_noi_bat' => $hocSinhNoiBat,
            ],
        ]);
    }
}

