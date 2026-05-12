<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChiTietLuyenTapSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Giả định: phien_id 1..6 đã tồn tại theo PhienLuyenTapSeeder; tu_vung_id theo TuVungSeeder (1..79)
        $chiTietLuyenTaps = [
            // Phiên 1 (phien_id = 1) - Phạm Thị Học, bài 1
            [
                'id' => 1,
                'phien_id' => 1,
                'tu_vung_id' => 1, // 'a'
                'file_ghi_am_url' => null,
                'van_ban_ai_nhan_dien' => 'a',
                'diem_tin_cay' => 0.98,
                'diem_chinh_xac' => 95,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => null,
                'created_at' => $now->copy()->subMinutes(29),
                'updated_at' => $now->copy()->subMinutes(29),
            ],
            [
                'id' => 2,
                'phien_id' => 1,
                'tu_vung_id' => 2, // 'ă'
                'file_ghi_am_url' => null,
                'van_ban_ai_nhan_dien' => 'ă',
                'diem_tin_cay' => 0.92,
                'diem_chinh_xac' => 88,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => null,
                'created_at' => $now->copy()->subMinutes(28),
                'updated_at' => $now->copy()->subMinutes(28),
            ],
            [
                'id' => 3,
                'phien_id' => 1,
                'tu_vung_id' => 4, // 'ba'
                'file_ghi_am_url' => 'uploads/recordings/phien1_ba_1.mp3',
                'van_ban_ai_nhan_dien' => 'ba',
                'diem_tin_cay' => 0.85,
                'diem_chinh_xac' => 80,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => null,
                'created_at' => $now->copy()->subMinutes(27),
                'updated_at' => $now->copy()->subMinutes(27),
            ],

            // Phiên 2 (phien_id = 2) - Phạm Thị Học, bài 4
            [
                'id' => 4,
                'phien_id' => 2,
                'tu_vung_id' => 19, // 'ai'
                'file_ghi_am_url' => 'uploads/recordings/phien2_ai_1.mp3',
                'van_ban_ai_nhan_dien' => 'ai',
                'diem_tin_cay' => 0.76,
                'diem_chinh_xac' => 70,
                'loi_am_dau' => false,
                'loi_van' => true,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => 'Phát âm ngắn hơn mẫu; cần kéo dài âm đôi.',
                'created_at' => $now->copy()->subDays(1)->subMinutes(19),
                'updated_at' => $now->copy()->subDays(1)->subMinutes(19),
            ],
            [
                'id' => 5,
                'phien_id' => 2,
                'tu_vung_id' => 22, // 'mai'
                'file_ghi_am_url' => 'uploads/recordings/phien2_mai_1.mp3',
                'van_ban_ai_nhan_dien' => 'mai',
                'diem_tin_cay' => 0.81,
                'diem_chinh_xac' => 75,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => null,
                'created_at' => $now->copy()->subDays(1)->subMinutes(18),
                'updated_at' => $now->copy()->subDays(1)->subMinutes(18),
            ],

            // Phiên 3 (phien_id = 3) - Đặng Minh Học, bài 3
            [
                'id' => 6,
                'phien_id' => 3,
                'tu_vung_id' => 13, // 'mẹ'
                'file_ghi_am_url' => 'uploads/recordings/phien3_me_1.mp3',
                'van_ban_ai_nhan_dien' => 'mẹ',
                'diem_tin_cay' => 0.95,
                'diem_chinh_xac' => 92,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => true,
                'chi_tiet_loi' => 'Thanh điệu đôi khi bị sai khi nói câu.',
                'created_at' => $now->copy()->subDays(2)->subMinutes(14),
                'updated_at' => $now->copy()->subDays(2)->subMinutes(14),
            ],

            // Phiên 4 (phien_id = 4) - Hoàng Thị Thảo, bài 6
            [
                'id' => 7,
                'phien_id' => 4,
                'tu_vung_id' => 31, // 'ma' (ngang)
                'file_ghi_am_url' => 'uploads/recordings/phien4_ma_1.mp3',
                'van_ban_ai_nhan_dien' => 'ma',
                'diem_tin_cay' => 0.99,
                'diem_chinh_xac' => 99,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => null,
                'created_at' => $now->copy()->subDays(3)->subMinutes(39),
                'updated_at' => $now->copy()->subDays(3)->subMinutes(39),
            ],

            // Phiên 5 (phien_id = 5) - Bùi Văn Tài, bài 8
            [
                'id' => 8,
                'phien_id' => 5,
                'tu_vung_id' => 43, // 'mèo'
                'file_ghi_am_url' => 'uploads/recordings/phien5_meo_1.mp3',
                'van_ban_ai_nhan_dien' => 'mèo',
                'diem_tin_cay' => 0.60,
                'diem_chinh_xac' => 55,
                'loi_am_dau' => true,
                'loi_van' => false,
                'loi_thanh_dieu' => false,
                'chi_tiet_loi' => 'Âm m chưa rõ, cần bài tập môi và hơi.',
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],

            // Phiên 6 (phien_id = 6) - Trịnh Thị Yêu, bài 10
            [
                'id' => 9,
                'phien_id' => 6,
                'tu_vung_id' => 55, // 'xin chào'
                'file_ghi_am_url' => 'uploads/recordings/phien6_xin_chao_1.mp3',
                'van_ban_ai_nhan_dien' => 'xin chào',
                'diem_tin_cay' => 0.82,
                'diem_chinh_xac' => 82,
                'loi_am_dau' => false,
                'loi_van' => false,
                'loi_thanh_dieu' => true,
                'chi_tiet_loi' => 'Ngữ điệu chào hỏi chưa tự nhiên; cần luyện ngữ điệu câu.',
                'created_at' => $now->copy()->subDays(1)->subHours(2),
                'updated_at' => $now->copy()->subDays(1)->subHours(2),
            ],
        ];

        DB::table('chi_tiet_luyen_taps')->upsert(
            $chiTietLuyenTaps,
            ['id'],
            [
                'phien_id',
                'tu_vung_id',
                'file_ghi_am_url',
                'van_ban_ai_nhan_dien',
                'diem_tin_cay',
                'diem_chinh_xac',
                'loi_am_dau',
                'loi_van',
                'loi_thanh_dieu',
                'chi_tiet_loi',
                'created_at',
                'updated_at'
            ]
        );
    }
}
