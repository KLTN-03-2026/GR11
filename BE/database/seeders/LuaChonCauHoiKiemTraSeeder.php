<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Lựa chọn trắc nghiệm: nội dung hiển thị đúng {@see TuVungSeeder::tu_chuan} (bai_hoc_id = 1).
 */
class LuaChonCauHoiKiemTraSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = DB::table('bai_kiem_tras')->where('bai_hoc_id', 1)->first();
        if (! $quiz) {
            return;
        }

        $mcqs = DB::table('cau_hoi_kiem_tras')
            ->where('bai_kiem_tra_id', $quiz->id)
            ->where('loai', 'mcq')
            ->orderBy('thu_tu')
            ->get();

        if ($mcqs->isEmpty()) {
            return;
        }

        $now = Carbon::now();

        $byThuTu = $mcqs->keyBy('thu_tu');

        $plans = [
            1 => [
                ['noi_dung' => 'â', 'la_dung' => true, 'thu_tu' => 1],
                ['noi_dung' => 'ă', 'la_dung' => false, 'thu_tu' => 2],
                ['noi_dung' => 'e', 'la_dung' => false, 'thu_tu' => 3],
                ['noi_dung' => 'o', 'la_dung' => false, 'thu_tu' => 4],
            ],
            2 => [
                ['noi_dung' => 'ă', 'la_dung' => true, 'thu_tu' => 1],
                ['noi_dung' => 'a', 'la_dung' => false, 'thu_tu' => 2],
                ['noi_dung' => 'â', 'la_dung' => false, 'thu_tu' => 3],
                ['noi_dung' => 'i', 'la_dung' => false, 'thu_tu' => 4],
            ],
            3 => [
                ['noi_dung' => 'ư', 'la_dung' => true, 'thu_tu' => 1],
                ['noi_dung' => 'u', 'la_dung' => false, 'thu_tu' => 2],
                ['noi_dung' => 'ơ', 'la_dung' => false, 'thu_tu' => 3],
                ['noi_dung' => 'ô', 'la_dung' => false, 'thu_tu' => 4],
            ],
            4 => [
                ['noi_dung' => 'ô', 'la_dung' => true, 'thu_tu' => 1],
                ['noi_dung' => 'o', 'la_dung' => false, 'thu_tu' => 2],
                ['noi_dung' => 'ơ', 'la_dung' => false, 'thu_tu' => 3],
                ['noi_dung' => 'a', 'la_dung' => false, 'thu_tu' => 4],
            ],
        ];

        foreach ($plans as $thuTuCau => $opts) {
            $cau = $byThuTu->get($thuTuCau);
            if (! $cau) {
                continue;
            }

            foreach ($opts as $o) {
                DB::table('lua_chon_cau_hois')->insert([
                    'cau_hoi_kiem_tra_id' => $cau->id,
                    'noi_dung' => $o['noi_dung'],
                    'la_dung' => $o['la_dung'],
                    'thu_tu' => $o['thu_tu'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
