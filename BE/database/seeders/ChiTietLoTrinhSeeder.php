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

        $chiTietLoTrinhs = [
            // Lộ trình 1 (hoc_vien_id => 4, giao_vien_id => 2) - các bài cơ bản
            [
                'lo_trinh_id' => 1,
                'bai_hoc_id' => 1,
                'thu_tu_uu_tien' => 1,
                'ghi_chu_gv' => 'Bắt đầu từ nguyên âm đơn, yêu cầu ghi âm bài tập.',
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'lo_trinh_id' => 1,
                'bai_hoc_id' => 2,
                'thu_tu_uu_tien' => 2,
                'ghi_chu_gv' => 'Tiếp theo là phụ âm đơn, chú ý vị trí môi.',
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'lo_trinh_id' => 1,
                'bai_hoc_id' => 14,
                'thu_tu_uu_tien' => 3,
                'ghi_chu_gv' => 'Kết thúc bằng bài kiểm tra cơ bản.',
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Lộ trình 2 (hoc_vien_id => 5, giao_vien_id => 3) - phát âm nâng cao
            [
                'lo_trinh_id' => 2,
                'bai_hoc_id' => 4,
                'thu_tu_uu_tien' => 1,
                'ghi_chu_gv' => 'Luyện nguyên âm đôi, chú ý phân biệt âm gần giống.',
                'ngay_tao' => $now->copy()->subDays(7),
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'lo_trinh_id' => 2,
                'bai_hoc_id' => 5,
                'thu_tu_uu_tien' => 2,
                'ghi_chu_gv' => 'Phụ âm khó: tập trung vào r, tr, ch.',
                'ngay_tao' => $now->copy()->subDays(7),
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'lo_trinh_id' => 2,
                'bai_hoc_id' => 12,
                'thu_tu_uu_tien' => 3,
                'ghi_chu_gv' => 'Bài chuyên sâu cho phân biệt r/gi/tr.',
                'ngay_tao' => $now->copy()->subDays(7),
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],

            // Lộ trình 3 (hoc_vien_id => 7, giao_vien_id => 2) - từ vựng cho trẻ em
            [
                'lo_trinh_id' => 3,
                'bai_hoc_id' => 3,
                'thu_tu_uu_tien' => 1,
                'ghi_chu_gv' => 'Bắt đầu với từ hàng ngày, kèm hình ảnh minh họa.',
                'ngay_tao' => $now->copy()->subDays(30),
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(30),
            ],
            [
                'lo_trinh_id' => 3,
                'bai_hoc_id' => 7,
                'thu_tu_uu_tien' => 2,
                'ghi_chu_gv' => 'Chủ đề gia đình, luyện ghép câu đơn giản.',
                'ngay_tao' => $now->copy()->subDays(30),
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(30),
            ],
            [
                'lo_trinh_id' => 3,
                'bai_hoc_id' => 8,
                'thu_tu_uu_tien' => 3,
                'ghi_chu_gv' => 'Chủ đề động vật, thêm bài tập nghe.',
                'ngay_tao' => $now->copy()->subDays(30),
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(30),
            ],
        ];

        DB::table('chi_tiet_lo_trinhs')->upsert(
            $chiTietLoTrinhs,
            ['lo_trinh_id', 'bai_hoc_id'],
            ['thu_tu_uu_tien', 'ghi_chu_gv', 'ngay_tao', 'updated_at']
        );
    }
}
