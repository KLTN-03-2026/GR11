<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TienDoHocTapSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Giả định: nguoi_dung_id (4..10) là học viên; tu_vung_id tồn tại theo TuVungSeeder (1..79)
        $tienDoHocTaps = [
            [
                'id' => 1,
                'nguoi_dung_id' => 4, // Phạm Thị Học
                'tu_vung_id' => 13, // mẹ
                'trang_thai' => 'dang_on_tap',
                'so_lan_luyen_tap' => 5,
                'diem_cao_nhat' => 85,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'tu_vung_id' => 14, // ba
                'trang_thai' => 'da_hoan_thanh',
                'so_lan_luyen_tap' => 8,
                'diem_cao_nhat' => 95,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 3,
                'nguoi_dung_id' => 5, // Ngô Văn Học (chưa kích hoạt) - vẫn có bản ghi tiến độ mẫu
                'tu_vung_id' => 19, // ai
                'trang_thai' => 'chua_on_tap',
                'so_lan_luyen_tap' => 0,
                'diem_cao_nhat' => 0,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 4,
                'nguoi_dung_id' => 6, // Vũ Thị Khóa (bị block)
                'tu_vung_id' => 25, // tr
                'trang_thai' => 'dang_on_tap',
                'so_lan_luyen_tap' => 2,
                'diem_cao_nhat' => 60,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 5,
                'nguoi_dung_id' => 7, // Đặng Minh Học
                'tu_vung_id' => 22, // mai
                'trang_thai' => 'dang_on_tap',
                'so_lan_luyen_tap' => 3,
                'diem_cao_nhat' => 72,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 6,
                'nguoi_dung_id' => 7,
                'tu_vung_id' => 23, // cao
                'trang_thai' => 'chua_on_tap',
                'so_lan_luyen_tap' => 0,
                'diem_cao_nhat' => 0,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 7,
                'nguoi_dung_id' => 8, // Hoàng Thị Thảo
                'tu_vung_id' => 31, // ma (ngang)
                'trang_thai' => 'da_hoan_thanh',
                'so_lan_luyen_tap' => 12,
                'diem_cao_nhat' => 98,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 8,
                'nguoi_dung_id' => 8,
                'tu_vung_id' => 32, // mà (huyền)
                'trang_thai' => 'dang_on_tap',
                'so_lan_luyen_tap' => 4,
                'diem_cao_nhat' => 78,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 9,
                'nguoi_dung_id' => 9, // Bùi Văn Tài
                'tu_vung_id' => 43, // mèo
                'trang_thai' => 'dang_on_tap',
                'so_lan_luyen_tap' => 1,
                'diem_cao_nhat' => 55,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 10,
                'nguoi_dung_id' => 9,
                'tu_vung_id' => 44, // chó
                'trang_thai' => 'chua_on_tap',
                'so_lan_luyen_tap' => 0,
                'diem_cao_nhat' => 0,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 11,
                'nguoi_dung_id' => 10, // Trịnh Thị Yêu (yêu cầu reset mật khẩu)
                'tu_vung_id' => 55, // xin chào
                'trang_thai' => 'dang_on_tap',
                'so_lan_luyen_tap' => 6,
                'diem_cao_nhat' => 82,
                'ngay_cap_nhat_cuoi' => $now,
            ],
            [
                'id' => 12,
                'nguoi_dung_id' => 10,
                'tu_vung_id' => 56, // tôi tên là
                'trang_thai' => 'chua_on_tap',
                'so_lan_luyen_tap' => 0,
                'diem_cao_nhat' => 0,
                'ngay_cap_nhat_cuoi' => $now,
            ],
        ];

        DB::table('tien_do_hoc_taps')->upsert(
            $tienDoHocTaps,
            ['nguoi_dung_id', 'tu_vung_id'],
            ['trang_thai', 'so_lan_luyen_tap', 'diem_cao_nhat', 'ngay_cap_nhat_cuoi']
        );
    }
}
