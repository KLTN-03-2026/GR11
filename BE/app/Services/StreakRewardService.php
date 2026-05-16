<?php

namespace App\Services;

use App\Models\ThongTinHocVien;
use Carbon\Carbon;

class StreakRewardService
{
    /** Điểm thưởng khi ghi nhận hoạt động học lần đầu trong ngày. */
    public const DIEM_THUONG_NGAY = 15;

    /**
     * Cập nhật streak và cộng điểm sau hoạt động học (điểm danh / hoàn thành phiên).
     *
     * @return array{
     *   streak_hien_tai: int,
     *   diem_tich_luy: int,
     *   ngay_hoc_cuoi_cung: string|null,
     *   da_diem_danh_hom_nay: bool,
     *   diem_vua_cong: int,
     *   diem_thuong_ngay: int
     * }
     */
    public function capNhatSauHoatDongHoc(int $nguoiDungId, int $diemTuHoatDong = 0): array
    {
        $tt = ThongTinHocVien::firstOrCreate(
            ['nguoi_dung_id' => $nguoiDungId],
            ['diem_tich_luy' => 0, 'streak_hien_tai' => 0, 'ngay_hoc_cuoi_cung' => null]
        );

        $today = Carbon::today()->toDateString();
        $last = $tt->ngay_hoc_cuoi_cung
            ? Carbon::parse($tt->ngay_hoc_cuoi_cung)->toDateString()
            : null;

        $diemTuHoatDong = max(0, $diemTuHoatDong);
        $diemThuongNgay = 0;

        if ($last === $today) {
            $tt->diem_tich_luy = (int) $tt->diem_tich_luy + $diemTuHoatDong;
            $tt->save();

            return $this->ketQua($tt, true, $diemTuHoatDong, 0);
        }

        $diemThuongNgay = self::DIEM_THUONG_NGAY;
        $tongCong = $diemTuHoatDong + $diemThuongNgay;

        if ($last && Carbon::parse($last)->addDay()->toDateString() === $today) {
            $tt->streak_hien_tai = (int) $tt->streak_hien_tai + 1;
        } else {
            $tt->streak_hien_tai = 1;
        }

        $tt->diem_tich_luy = (int) $tt->diem_tich_luy + $tongCong;
        $tt->ngay_hoc_cuoi_cung = $today;
        $tt->save();

        return $this->ketQua($tt, false, $tongCong, $diemThuongNgay);
    }

    /**
     * @return array{
     *   streak_hien_tai: int,
     *   diem_tich_luy: int,
     *   ngay_hoc_cuoi_cung: string|null,
     *   da_diem_danh_hom_nay: bool,
     *   diem_vua_cong: int,
     *   diem_thuong_ngay: int
     * }
     */
    private function ketQua(ThongTinHocVien $tt, bool $daDiemDanhTruocDo, int $diemVuaCong, int $diemThuongNgay): array
    {
        $today = Carbon::today()->toDateString();
        $last = $tt->ngay_hoc_cuoi_cung
            ? Carbon::parse($tt->ngay_hoc_cuoi_cung)->toDateString()
            : null;

        return [
            'streak_hien_tai' => (int) $tt->streak_hien_tai,
            'diem_tich_luy' => (int) $tt->diem_tich_luy,
            'ngay_hoc_cuoi_cung' => $tt->ngay_hoc_cuoi_cung,
            'da_diem_danh_hom_nay' => $last === $today,
            'da_diem_danh_truoc_do' => $daDiemDanhTruocDo,
            'diem_vua_cong' => $diemVuaCong,
            'diem_thuong_ngay' => $diemThuongNgay,
        ];
    }
}
