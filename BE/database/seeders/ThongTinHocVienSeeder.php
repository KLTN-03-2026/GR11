<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThongTinHocVienSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $hocVienIds = DB::table('nguoi_dungs')->where('vai_tro_id', 3)->orderBy('id')->pluck('id');

        $mau = [
            4 => ['diem_tich_luy' => 180, 'streak_hien_tai' => 6, 'ngay' => 1],
            5 => ['diem_tich_luy' => 0, 'streak_hien_tai' => 0, 'ngay' => null],
            6 => ['diem_tich_luy' => 40, 'streak_hien_tai' => 0, 'ngay' => 30],
            7 => ['diem_tich_luy' => 95, 'streak_hien_tai' => 4, 'ngay' => 2],
            8 => ['diem_tich_luy' => 240, 'streak_hien_tai' => 14, 'ngay' => 0],
            9 => ['diem_tich_luy' => 55, 'streak_hien_tai' => 2, 'ngay' => 7],
            10 => ['diem_tich_luy' => 20, 'streak_hien_tai' => 0, 'ngay' => 15],
        ];

        foreach ($hocVienIds as $nid) {
            $cfg = $mau[(int) $nid] ?? ['diem_tich_luy' => 10, 'streak_hien_tai' => 0, 'ngay' => 3];
            $ngay = $cfg['ngay'] === null ? null : $now->copy()->subDays((int) $cfg['ngay'])->toDateString();

            $row = [
                'nguoi_dung_id' => $nid,
                'diem_tich_luy' => $cfg['diem_tich_luy'],
                'streak_hien_tai' => $cfg['streak_hien_tai'],
                'ngay_hoc_cuoi_cung' => $ngay,
                'updated_at' => $now,
            ];

            if (DB::table('thong_tin_hoc_viens')->where('nguoi_dung_id', $nid)->exists()) {
                DB::table('thong_tin_hoc_viens')->where('nguoi_dung_id', $nid)->update($row);
            } else {
                DB::table('thong_tin_hoc_viens')->insert(array_merge($row, [
                    'created_at' => $now,
                ]));
            }
        }
    }
}
