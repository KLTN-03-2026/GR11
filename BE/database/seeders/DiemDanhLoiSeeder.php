<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiemDanhLoiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Giả định: nguoi_dung_id 4..10 (học viên mẫu); tu_vung_id theo TuVungSeeder (1..79)
        $diemDanhLois = [
            [
                'id' => 1,
                'nguoi_dung_id' => 4, // Phạm Thị Học
                'tu_vung_id' => 25, // tr
                'muc_do_uu_tien' => 'cao',
                'ghi_chu' => 'Khó phát âm âm đầu "tr", cần bài tập lưỡi và môi.',
                'da_hoan_thanh' => false,
                'ngay_danh_dau' => $now->copy()->subDays(10),
                'ngay_hoan_thanh' => null,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'tu_vung_id' => 26, // ch
                'muc_do_uu_tien' => 'binh_thuong',
                'ghi_chu' => 'Nhớ đặt lưỡi sát vòm miệng khi phát âm.',
                'da_hoan_thanh' => true,
                'ngay_danh_dau' => $now->copy()->subDays(20),
                'ngay_hoan_thanh' => $now->copy()->subDays(5),
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nguoi_dung_id' => 5, // Ngô Văn Học (chưa kích hoạt)
                'tu_vung_id' => 19, // ai
                'muc_do_uu_tien' => 'thap',
                'ghi_chu' => 'Chưa bắt đầu luyện; ưu tiên thấp.',
                'da_hoan_thanh' => false,
                'ngay_danh_dau' => $now->copy()->subDays(2),
                'ngay_hoan_thanh' => null,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'nguoi_dung_id' => 6, // Vũ Thị Khóa (bị block)
                'tu_vung_id' => 27, // r
                'muc_do_uu_tien' => 'cao',
                'ghi_chu' => 'Thường xuyên phát âm sai "r", cần lộ trình chuyên sâu.',
                'da_hoan_thanh' => false,
                'ngay_danh_dau' => $now->copy()->subDays(30),
                'ngay_hoan_thanh' => null,
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'nguoi_dung_id' => 7, // Đặng Minh Học
                'tu_vung_id' => 13, // mẹ
                'muc_do_uu_tien' => 'binh_thuong',
                'ghi_chu' => 'Cần lặp lại 3 lần/ngày.',
                'da_hoan_thanh' => true,
                'ngay_danh_dau' => $now->copy()->subDays(7),
                'ngay_hoan_thanh' => $now->copy()->subDays(1),
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'nguoi_dung_id' => 8, // Hoàng Thị Thảo
                'tu_vung_id' => 31, // ma (ngang)
                'muc_do_uu_tien' => 'binh_thuong',
                'ghi_chu' => 'Đã hoàn thành phần cơ bản, chuyển sang thanh huyền.',
                'da_hoan_thanh' => true,
                'ngay_danh_dau' => $now->copy()->subDays(15),
                'ngay_hoan_thanh' => $now->copy()->subDays(3),
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'nguoi_dung_id' => 9, // Bùi Văn Tài
                'tu_vung_id' => 43, // mèo
                'muc_do_uu_tien' => 'thap',
                'ghi_chu' => 'Ưu tiên thấp, mới bắt đầu.',
                'da_hoan_thanh' => false,
                'ngay_danh_dau' => $now->copy()->subDays(1),
                'ngay_hoan_thanh' => null,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'nguoi_dung_id' => 10, // Trịnh Thị Yêu
                'tu_vung_id' => 55, // xin chào
                'muc_do_uu_tien' => 'cao',
                'ghi_chu' => 'Cần luyện ngữ điệu khi chào hỏi.',
                'da_hoan_thanh' => false,
                'ngay_danh_dau' => $now->copy()->subDays(4),
                'ngay_hoan_thanh' => null,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now,
            ],
        ];

        DB::table('diem_danh_lois')->upsert(
            $diemDanhLois,
            ['id'],
            ['nguoi_dung_id', 'tu_vung_id', 'muc_do_uu_tien', 'ghi_chu', 'da_hoan_thanh', 'ngay_danh_dau', 'ngay_hoan_thanh', 'created_at', 'updated_at']
        );
    }
}
