<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use App\Models\QuanHeGvHv;
use App\Models\ThongTinHocVien;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TrangChuController extends Controller
{
    public function index(): JsonResponse
    {
        $luotLuyenTheoDanhMuc = PhienLuyenTap::query()
            ->join('bai_hocs', 'bai_hocs.id', '=', 'phien_luyen_taps.bai_hoc_id')
            ->whereColumn('bai_hocs.danh_muc_id', 'danh_muc_bai_hocs.id')
            ->where('bai_hocs.trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)
            ->selectRaw('count(phien_luyen_taps.id)');

        // Chủ đề nổi bật: dựa theo số phiên luyện thuộc các bài học trong danh mục (fallback: số bài học)
        $chuDeNoiBat = DanhMucBaiHoc::query()
            ->where('trang_thai', DanhMucBaiHoc::TRANG_THAI_HIEN_THI)
            ->withCount([
                'baiHocs as so_luong_bai_hoc' => function ($q): void {
                    $q->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG);
                },
            ])
            ->addSelect(['luot_luyen' => $luotLuyenTheoDanhMuc])
            ->orderByDesc($luotLuyenTheoDanhMuc)
            ->orderByDesc('so_luong_bai_hoc')
            ->orderBy('thu_tu')
            ->limit(6)
            ->get([
                'id',
                'ten_danh_muc',
                'slug_danh_muc',
                'mo_ta',
                'thu_tu',
            ]);

        $luotLuyenTheoBaiHoc = PhienLuyenTap::query()
            ->whereColumn('phien_luyen_taps.bai_hoc_id', 'bai_hocs.id')
            ->selectRaw('count(phien_luyen_taps.id)');

        // Bài học nổi bật: dựa theo số phiên luyện (fallback: bài mới)
        $baiHocNoiBat = BaiHoc::query()
            ->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)
            ->with([
                'danhMuc:id,ten_danh_muc,slug_danh_muc',
            ])
            ->withCount([
                'tuVungs as so_tu_vung',
            ])
            ->addSelect(['luot_luyen' => $luotLuyenTheoBaiHoc])
            ->orderByDesc($luotLuyenTheoBaiHoc)
            ->orderByDesc('bai_hocs.id')
            ->limit(8)
            ->get()
            ->values();

        // Học sinh nổi bật: top bảng xếp hạng (cùng thứ tự /leaderboard?type=points)
        $hocSinhNoiBat = ThongTinHocVien::query()
            ->join('nguoi_dungs as u', 'thong_tin_hoc_viens.nguoi_dung_id', '=', 'u.id')
            ->where('u.vai_tro_id', NguoiDung::ROLE_USER)
            ->orderByDesc('thong_tin_hoc_viens.diem_tich_luy')
            ->orderByDesc('thong_tin_hoc_viens.streak_hien_tai')
            ->limit(6)
            ->get([
                'u.id',
                'u.ho_ten',
                'u.anh_dai_dien',
                'thong_tin_hoc_viens.streak_hien_tai',
                'thong_tin_hoc_viens.diem_tich_luy',
            ])
            ->values()
            ->map(function ($row, int $index) {
                $hvId = (int) $row->id;
                $streak = (int) $row->streak_hien_tai;
                $diemTichLuy = (int) $row->diem_tich_luy;
                $thuHang = $index + 1;

                $thongKePhien = PhienLuyenTap::query()
                    ->where('nguoi_dung_id', $hvId)
                    ->selectRaw('COUNT(*) as so_phien, COUNT(DISTINCT bai_hoc_id) as so_bai_hoc, AVG(CASE WHEN tong_diem IS NOT NULL THEN tong_diem END) as diem_tb')
                    ->first();

                $soPhien = (int) ($thongKePhien->so_phien ?? 0);
                $soBaiHoc = (int) ($thongKePhien->so_bai_hoc ?? 0);
                $diemTb = round((float) ($thongKePhien->diem_tb ?? 0), 1);

                $giaoVien = QuanHeGvHv::query()
                    ->where('quan_he_gv_hvs.hoc_vien_id', $hvId)
                    ->where('quan_he_gv_hvs.trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
                    ->join('nguoi_dungs as gv', 'gv.id', '=', 'quan_he_gv_hvs.giao_vien_id')
                    ->orderByDesc('quan_he_gv_hvs.ngay_ket_noi')
                    ->first(['gv.ho_ten as giao_vien_ten', 'gv.anh_dai_dien as giao_vien_anh']);

                $moTa = 'Hạng #'.$thuHang.' bảng xếp hạng · '.$diemTichLuy.' XP';
                if ($streak > 0) {
                    $moTa .= ' · 🔥 '.$streak.' ngày streak';
                }

                return [
                    'id' => $hvId,
                    'thu_hang' => $thuHang,
                    'ho_ten' => $row->ho_ten,
                    'anh_dai_dien' => $this->resolveAvatarUrl($row->anh_dai_dien),
                    'giao_vien_ten' => $giaoVien?->giao_vien_ten,
                    'giao_vien_anh' => $this->resolveAvatarUrl($giaoVien?->giao_vien_anh),
                    'mo_ta' => $moTa,
                    'so_phien' => $soPhien,
                    'so_bai_hoc' => $soBaiHoc,
                    'streak_hien_tai' => $streak,
                    'diem_tich_luy' => $diemTichLuy,
                    'diem_trung_binh' => $diemTb,
                    'diem_phan_tram' => (int) round($diemTb),
                    'diem_hien_thi' => number_format($diemTb / 10, 1, '.', '').'/10',
                ];
            })
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

    private function resolveAvatarUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(Storage::url(ltrim($path, '/')));
    }
}
