<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThongTinHocVienSeeder extends Seeder
{
    private const HOC_VIEN = [
        'giabao.student@gmail.com' => ['ngay_sinh' => '2019-05-20', 'diem_tich_luy' => 420, 'streak' => 12],
        'minhan.hv@gmail.com' => ['ngay_sinh' => '2018-11-03', 'diem_tich_luy' => 280, 'streak' => 5],
        'thaovy.kid@outlook.com' => ['ngay_sinh' => '2020-01-15', 'diem_tich_luy' => 150, 'streak' => 3],
        'hoanglong.phuong@gmail.com' => ['ngay_sinh' => '2019-08-22', 'diem_tich_luy' => 310, 'streak' => 8],
        'khanhlinh.hv@gmail.com' => ['ngay_sinh' => '2018-04-10', 'diem_tich_luy' => 390, 'streak' => 15],
        'haanh.hv@gmail.com' => ['ngay_sinh' => '2019-12-03', 'diem_tich_luy' => 220, 'streak' => 6],
        'tunglam.kid@gmail.com' => ['ngay_sinh' => '2020-03-18', 'diem_tich_luy' => 180, 'streak' => 4],
        'hientrang.hv@gmail.com' => ['ngay_sinh' => '2018-07-25', 'diem_tich_luy' => 350, 'streak' => 10],
    ];

    /**
     * Điểm tích lũy — streak — ngày học gần nhất: phản ánh học đều / mới tập.
     */
    public function run(): void
    {
        foreach (self::HOC_VIEN as $email => $meta) {
            $hv = DB::table('nguoi_dungs')->where('email', $email)->first(['id']);
            if (! $hv) {
                continue;
            }
            DB::table('thong_tin_hoc_viens')->updateOrInsert(
                ['nguoi_dung_id' => $hv->id],
                [
                    'ngay_sinh' => $meta['ngay_sinh'],
                    'diem_tich_luy' => $meta['diem_tich_luy'],
                    'streak_hien_tai' => $meta['streak'],
                    'ngay_hoc_cuoi_cung' => Carbon::now()->subDays(random_int(0, 4))->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
