<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoiPremiumSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('goi_premiums')->insertOrIgnore([
            [
                'ten_goi' => 'Premium Học Viên',
                'doi_tuong' => 'hoc_vien',
                'mo_ta' => 'Mở khóa gợi ý AI nâng cao và báo cáo tiến độ hàng tuần cho học viên.',
                'gia' => 149000,
                'thoi_han_ngay' => 30,
                'tinh_nang' => json_encode([]),
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_goi' => 'Premium Giáo Viên',
                'doi_tuong' => 'giao_vien',
                'mo_ta' => 'Tăng giới hạn học viên lên 80, mở khóa gợi ý AI nâng cấp và báo cáo hiệu suất từng học viên.',
                'gia' => 149000,
                'thoi_han_ngay' => 30,
                'tinh_nang' => json_encode([
                    'gioi_han_hoc_vien' => 80,
                ]),
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
