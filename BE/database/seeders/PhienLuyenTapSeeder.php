<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhienLuyenTapSeeder extends Seeder
{
    private const HOC_VIEN_EMAILS = [
        'giabao.student@gmail.com',
        'minhan.hv@gmail.com',
        'thaovy.kid@outlook.com',
        'hoanglong.phuong@gmail.com',
        'khanhlinh.hv@gmail.com',
    ];

    /**
     * Phiên luyện tập theo từng bài — nhiều ngày liên tiếp, điểm có xu hướng tăng.
     */
    public function run(): void
    {
        $hocVienIds = DB::table('nguoi_dungs')->whereIn('email', self::HOC_VIEN_EMAILS)->pluck('id');
        $baiHocIds = DB::table('bai_hocs')->orderBy('thu_tu')->pluck('id');

        if ($hocVienIds->isEmpty() || $baiHocIds->isEmpty()) {
            return;
        }

        $phienIds = DB::table('phien_luyen_taps')->whereIn('nguoi_dung_id', $hocVienIds)->pluck('id');
        if ($phienIds->isNotEmpty()) {
            DB::table('chi_tiet_luyen_taps')->whereIn('phien_id', $phienIds)->delete();
        }
        DB::table('phien_luyen_taps')->whereIn('nguoi_dung_id', $hocVienIds)->delete();

        mt_srand(100);

        foreach ($hocVienIds as $hvId) {
            $soPhien = random_int(9, 14);
            $baseScore = 62;

            for ($p = 0; $p < $soPhien; $p++) {
                $ngay = Carbon::now()->subDays($soPhien - $p);
                $baiId = $baiHocIds[$p % $baiHocIds->count()];
                $baseScore = min(98, $baseScore + random_int(-2, 6));

                DB::table('phien_luyen_taps')->insert([
                    'nguoi_dung_id' => $hvId,
                    'bai_hoc_id' => $baiId,
                    'tong_diem' => $baseScore,
                    'thoi_gian_bat_dau' => $ngay->copy()->setTime(8, random_int(0, 25)),
                    'thoi_gian_ket_thuc' => $ngay->copy()->setTime(8, random_int(30, 55)),
                    'ngay_tao' => $ngay,
                ]);
            }
        }
    }
}
