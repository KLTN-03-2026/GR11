<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuanHeGvHvSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $quanHeGvHvs = [
            [
                'id' => 1,
                'giao_vien_id' => 2, // Trần Thị Giáo Viên
                'hoc_vien_id' => 4,  // Phạm Thị Học
                'trang_thai' => 1,
                'ngay_ket_noi' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'giao_vien_id' => 2,
                'hoc_vien_id' => 5,  // Ngô Văn Học
                'trang_thai' => 1,
                'ngay_ket_noi' => $now->copy()->subDays(2),
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'id' => 3,
                'giao_vien_id' => 3, // Lê Văn Dạy
                'hoc_vien_id' => 7,  // Đặng Minh Học
                'trang_thai' => 1,
                'ngay_ket_noi' => $now->copy()->subDays(5),
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => 4,
                'giao_vien_id' => 3,
                'hoc_vien_id' => 8,  // Hoàng Thị Thảo
                'trang_thai' => 1,
                'ngay_ket_noi' => $now->copy()->subDays(10),
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'id' => 5,
                'giao_vien_id' => 2,
                'hoc_vien_id' => 9,  // Bùi Văn Tài
                'trang_thai' => 1,
                'ngay_ket_noi' => $now->copy()->subDays(1),
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('quan_he_gv_hvs')->upsert(
            $quanHeGvHvs,
            ['giao_vien_id', 'hoc_vien_id'],
            ['trang_thai', 'ngay_ket_noi', 'updated_at']
        );
    }
}
