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

        $loTrinhCaNhans = [
            [
                'id' => 1,
                'hoc_vien_id' => 4, // Phạm Thị Học (học viên)
                'giao_vien_id' => 2, // Trần Thị Giáo Viên (giáo viên)
                'ten_lo_trinh' => 'Lộ trình Tiếng Anh cơ bản',
                'ngay_tao' => $now->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'hoc_vien_id' => 5, // Ngô Văn Học (học viên)
                'giao_vien_id' => 3, // Lê Văn Dạy (giáo viên)
                'ten_lo_trinh' => 'Lộ trình Phát âm nâng cao',
                'ngay_tao' => $now->copy()->subDays(7)->toDateString(),
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'id' => 3,
                'hoc_vien_id' => 7, // Đặng Minh Học (học viên)
                'giao_vien_id' => 2, // Trần Thị Giáo Viên (giáo viên)
                'ten_lo_trinh' => 'Lộ trình Từ vựng cho trẻ em',
                'ngay_tao' => $now->copy()->subDays(30)->toDateString(),
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(30),
            ],
        ];

        DB::table('lo_trinh_ca_nhans')->upsert(
            $loTrinhCaNhans,
            ['id'],
            ['hoc_vien_id', 'giao_vien_id', 'ten_lo_trinh', 'ngay_tao', 'updated_at']
        );
    }
}
