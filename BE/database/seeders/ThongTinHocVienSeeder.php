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

        // Dựa trên NguoiDungSeeder: các nguoi_dung_id học viên mẫu là 4..10
        $thongTinHocViens = [
            [
                'id' => 1,
                'nguoi_dung_id' => 4, // Phạm Thị Học
                'diem_tich_luy' => 120,
                'streak_hien_tai' => 5,
                'ngay_hoc_cuoi_cung' => $now->copy()->subDays(1)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nguoi_dung_id' => 5, // Ngô Văn Học (chưa kích hoạt)
                'diem_tich_luy' => 0,
                'streak_hien_tai' => 0,
                'ngay_hoc_cuoi_cung' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nguoi_dung_id' => 6, // Vũ Thị Khóa (bị block)
                'diem_tich_luy' => 30,
                'streak_hien_tai' => 0,
                'ngay_hoc_cuoi_cung' => $now->copy()->subDays(30)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'nguoi_dung_id' => 7, // Đặng Minh Học
                'diem_tich_luy' => 75,
                'streak_hien_tai' => 3,
                'ngay_hoc_cuoi_cung' => $now->copy()->subDays(2)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'nguoi_dung_id' => 8, // Hoàng Thị Thảo
                'diem_tich_luy' => 200,
                'streak_hien_tai' => 12,
                'ngay_hoc_cuoi_cung' => $now->copy()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'nguoi_dung_id' => 9, // Bùi Văn Tài
                'diem_tich_luy' => 45,
                'streak_hien_tai' => 1,
                'ngay_hoc_cuoi_cung' => $now->copy()->subDays(7)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'nguoi_dung_id' => 10, // Trịnh Thị Yêu (yêu cầu reset mật khẩu)
                'diem_tich_luy' => 10,
                'streak_hien_tai' => 0,
                'ngay_hoc_cuoi_cung' => $now->copy()->subDays(15)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('thong_tin_hoc_viens')->upsert(
            $thongTinHocViens,
            ['id'],
            ['nguoi_dung_id', 'diem_tich_luy', 'streak_hien_tai', 'ngay_hoc_cuoi_cung', 'updated_at']
        );
    }
}
