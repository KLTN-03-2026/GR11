<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuanHeGvHvSeeder extends Seeder
{
    /**
     * Mỗi học viên gắn một giáo viên chủ nhiệm (phân bổ thực tế).
     */
    public function run(): void
    {
        $gv1 = DB::table('nguoi_dungs')->where('email', 'maiphuong.gv@gmail.com')->first();
        $gv2 = DB::table('nguoi_dungs')->where('email', 'thuylinh.gv@gmail.com')->first();
        $gv3 = DB::table('nguoi_dungs')->where('email', 'tranphong.gv@gmail.com')->first();

        $cap = [
            ['gv' => $gv1, 'hv_email' => 'giabao.student@gmail.com'],
            ['gv' => $gv2 ?? $gv1, 'hv_email' => 'minhan.hv@gmail.com'],
            ['gv' => $gv1, 'hv_email' => 'thaovy.kid@outlook.com'],
            ['gv' => $gv2 ?? $gv1, 'hv_email' => 'hoanglong.phuong@gmail.com'],
            ['gv' => $gv1, 'hv_email' => 'khanhlinh.hv@gmail.com'],
            ['gv' => $gv3 ?? $gv2, 'hv_email' => 'haanh.hv@gmail.com'],
            ['gv' => $gv2 ?? $gv1, 'hv_email' => 'tunglam.kid@gmail.com'],
            ['gv' => $gv3 ?? $gv1, 'hv_email' => 'hientrang.hv@gmail.com'],
        ];

        foreach ($cap as $row) {
            if (! $row['gv']) {
                continue;
            }
            $hv = DB::table('nguoi_dungs')->where('email', $row['hv_email'])->first();
            if (! $hv) {
                continue;
            }
            DB::table('quan_he_gv_hvs')->updateOrInsert(
                [
                    'giao_vien_id' => $row['gv']->id,
                    'hoc_vien_id' => $hv->id,
                ],
                [
                    'trang_thai' => 'dang_theo_doi',
                    'ngay_ket_noi' => now()->subDays(random_int(5, 120)),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
