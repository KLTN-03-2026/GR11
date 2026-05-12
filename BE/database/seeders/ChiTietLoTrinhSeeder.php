<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChiTietLoTrinhSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [];

        $loTrinhIds = DB::table('lo_trinh_ca_nhans')
            ->where('ten_lo_trinh', 'Lộ trình nhập môn (Mặc định)')
            ->pluck('id');

        foreach ($loTrinhIds as $loTrinhId) {
            $data[] = [
                'lo_trinh_id'    => $loTrinhId,
                'bai_hoc_id'     => 1,
                'thu_tu_uu_tien' => 1,
                'ghi_chu_gv'     => 'Bài mở đầu, làm quen kiến thức.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ];

            $data[] = [
                'lo_trinh_id'    => $loTrinhId,
                'bai_hoc_id'     => 2,
                'thu_tu_uu_tien' => 2,
                'ghi_chu_gv'     => 'Bài thực hành cơ bản.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ];

            $data[] = [
                'lo_trinh_id'    => $loTrinhId,
                'bai_hoc_id'     => 3,
                'thu_tu_uu_tien' => 3,
                'ghi_chu_gv'     => 'Kiểm tra kiến thức nhập môn.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        DB::table('chi_tiet_lo_trinhs')->insert($data);
    }
}
