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

        $defs = [
            ['nguoi_dung_id' => 4, 'bai_hoc_id' => 2, 'tu_chuan' => 'mẹ', 'trang_thai' => 1, 'so_lan' => 6, 'diem' => 88],
            ['nguoi_dung_id' => 4, 'bai_hoc_id' => 2, 'tu_chuan' => 'ba', 'trang_thai' => 2, 'so_lan' => 9, 'diem' => 94],
            ['nguoi_dung_id' => 5, 'bai_hoc_id' => 3, 'tu_chuan' => 'tủ', 'trang_thai' => 0, 'so_lan' => 0, 'diem' => 0],
            ['nguoi_dung_id' => 6, 'bai_hoc_id' => 4, 'tu_chuan' => 'tre', 'trang_thai' => 1, 'so_lan' => 3, 'diem' => 62],
            ['nguoi_dung_id' => 7, 'bai_hoc_id' => 3, 'tu_chuan' => 'thỏ', 'trang_thai' => 1, 'so_lan' => 4, 'diem' => 74],
            ['nguoi_dung_id' => 7, 'bai_hoc_id' => 3, 'tu_chuan' => 'nư', 'trang_thai' => 0, 'so_lan' => 0, 'diem' => 0],
            ['nguoi_dung_id' => 8, 'bai_hoc_id' => 6, 'tu_chuan' => 'la', 'trang_thai' => 2, 'so_lan' => 14, 'diem' => 98],
            ['nguoi_dung_id' => 8, 'bai_hoc_id' => 6, 'tu_chuan' => 'là', 'trang_thai' => 1, 'so_lan' => 5, 'diem' => 80],
            ['nguoi_dung_id' => 9, 'bai_hoc_id' => 19, 'tu_chuan' => 'mèo', 'trang_thai' => 1, 'so_lan' => 2, 'diem' => 58],
            ['nguoi_dung_id' => 9, 'bai_hoc_id' => 19, 'tu_chuan' => 'chó', 'trang_thai' => 0, 'so_lan' => 0, 'diem' => 0],
            ['nguoi_dung_id' => 10, 'bai_hoc_id' => 37, 'tu_chuan' => 'xin chào', 'trang_thai' => 1, 'so_lan' => 7, 'diem' => 84],
            ['nguoi_dung_id' => 10, 'bai_hoc_id' => 37, 'tu_chuan' => 'tạm biệt', 'trang_thai' => 0, 'so_lan' => 0, 'diem' => 0],
        ];

        $rows = [];
        foreach ($defs as $d) {
            $tid = TuVungLookup::id((int) $d['bai_hoc_id'], (string) $d['tu_chuan']);
            if (! $tid) {
                continue;
            }
            $rows[] = [
                'nguoi_dung_id' => $d['nguoi_dung_id'],
                'tu_vung_id' => $tid,
                'trang_thai' => $d['trang_thai'],
                'so_lan_luyen_tap' => $d['so_lan'],
                'diem_cao_nhat' => $d['diem'],
                'ngay_cap_nhat_cuoi' => $now,
            ];
        }

        if ($rows === []) {
            return;
        }

        DB::table('tien_do_hoc_taps')->upsert(
            $rows,
            ['nguoi_dung_id', 'tu_vung_id'],
            ['trang_thai', 'so_lan_luyen_tap', 'diem_cao_nhat', 'ngay_cap_nhat_cuoi']
        );
    }
}
