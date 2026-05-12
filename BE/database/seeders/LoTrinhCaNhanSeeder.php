<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoTrinhCaNhanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [];
        // Gán thêm lộ trình mặc định cho TOÀN BỘ học viên từ 4-10
        foreach (range(4, 10) as $id) {
            $data[] = [
                'hoc_vien_id'  => $id,
                'giao_vien_id' => 2,
                'ten_lo_trinh' => 'Lộ trình cơ bản',
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        $ca_nhan = [
            ['hoc_vien_id' => 4, 'giao_vien_id' => 2, 'ten_lo_trinh' => 'Lộ trình đồ dùng cơ bản', 'created_at' => $now, 'updated_at' => $now],
            ['hoc_vien_id' => 5, 'giao_vien_id' => 3, 'ten_lo_trinh' => 'Lộ trình Phát âm nâng cao', 'created_at' => $now, 'updated_at' => $now],
        ];
        $data = array_merge($data, $ca_nhan);
        DB::table('lo_trinh_ca_nhans')->insert($data);
    }
}
