<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LichSuLoiPhatAmSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Giả định: nguoi_dung_id 4..10 (học viên mẫu); tu_vung_id theo TuVungSeeder (1..79)
        $lichSuLoiPhatAms = [
            [
                'id' => 1,
                'nguoi_dung_id' => 4,
                'tu_vung_id' => 25, // tr
                'phien_id' => null,
                'loai_loi' => 'am_dau',
                'so_lan_mac_loi' => 5,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(1),
                'chi_tiet_loi' => 'Phát âm thành "ch" thay vì "tr"; cần bài tập đặt lưỡi.',
                'trang_thai' => 'chua_on_tap',
                'ngay_tao' => $now->copy()->subDays(10),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'tu_vung_id' => 26, // ch
                'phien_id' => null,
                'loai_loi' => 'am_dau',
                'so_lan_mac_loi' => 2,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(5),
                'chi_tiet_loi' => 'Âm "ch" hơi yếu, cần tăng cường bài tập bật hơi.',
                'trang_thai' => 'dang_on_tap',
                'ngay_tao' => $now->copy()->subDays(20),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 3,
                'nguoi_dung_id' => 5,
                'tu_vung_id' => 19, // ai
                'phien_id' => null,
                'loai_loi' => 'van',
                'so_lan_mac_loi' => 1,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(2),
                'chi_tiet_loi' => 'Nhầm nguyên âm đôi; phát âm ngắn hơn so với mẫu.',
                'trang_thai' => 'chua_on_tap',
                'ngay_tao' => $now->copy()->subDays(2),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 4,
                'nguoi_dung_id' => 6,
                'tu_vung_id' => 27, // r
                'phien_id' => null,
                'loai_loi' => 'am_dau',
                'so_lan_mac_loi' => 8,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(1),
                'chi_tiet_loi' => 'Thay "r" bằng "g" hoặc lược âm; cần lộ trình chuyên sâu.',
                'trang_thai' => 'dang_on_tap',
                'ngay_tao' => $now->copy()->subDays(30),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 5,
                'nguoi_dung_id' => 7,
                'tu_vung_id' => 13, // mẹ
                'phien_id' => null,
                'loai_loi' => 'thanh_dieu',
                'so_lan_mac_loi' => 3,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(3),
                'chi_tiet_loi' => 'Thanh điệu đôi khi bị sai (ngang vs huyền).',
                'trang_thai' => 'da_phat_hien_on_tap',
                'ngay_tao' => $now->copy()->subDays(7),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 6,
                'nguoi_dung_id' => 8,
                'tu_vung_id' => 31, // ma (ngang)
                'phien_id' => null,
                'loai_loi' => 'thanh_dieu',
                'so_lan_mac_loi' => 1,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(12),
                'chi_tiet_loi' => 'Đã cải thiện; theo dõi tiếp để chuyển trạng thái hoàn thành.',
                'trang_thai' => 'dang_on_tap',
                'ngay_tao' => $now->copy()->subDays(15),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 7,
                'nguoi_dung_id' => 9,
                'tu_vung_id' => 43, // mèo
                'phien_id' => null,
                'loai_loi' => 'am_dau',
                'so_lan_mac_loi' => 2,
                'lan_mac_loi_gan_nhat' => $now->copy()->subHours(20),
                'chi_tiet_loi' => 'Phát âm "m" chưa rõ; cần bài tập môi.',
                'trang_thai' => 'chua_on_tap',
                'ngay_tao' => $now->copy()->subDays(1),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 8,
                'nguoi_dung_id' => 10,
                'tu_vung_id' => 55, // xin chào
                'phien_id' => null,
                'loai_loi' => 'ngu_diệu', // lưu ý: migration không giới hạn giá trị, dùng 'ngu_diệu' để minh họa
                'so_lan_mac_loi' => 4,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(4),
                'chi_tiet_loi' => 'Ngữ điệu chào hỏi chưa tự nhiên; cần luyện ngữ điệu câu.',
                'trang_thai' => 'dang_on_tap',
                'ngay_tao' => $now->copy()->subDays(4),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 9,
                'nguoi_dung_id' => 4,
                'tu_vung_id' => 12, // ví dụ từ khác
                'phien_id' => null,
                'loai_loi' => 'van',
                'so_lan_mac_loi' => 1,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(6),
                'chi_tiet_loi' => 'Nhầm vần; cần bài tập nối âm.',
                'trang_thai' => 'chua_on_tap',
                'ngay_tao' => $now->copy()->subDays(6),
                'ngay_cap_nhat' => $now,
            ],
            [
                'id' => 10,
                'nguoi_dung_id' => 8,
                'tu_vung_id' => 32, // mà (huyền)
                'phien_id' => null,
                'loai_loi' => 'thanh_dieu',
                'so_lan_mac_loi' => 2,
                'lan_mac_loi_gan_nhat' => $now->copy()->subDays(2),
                'chi_tiet_loi' => 'Sai thanh khi nói câu dài; cần luyện nối câu.',
                'trang_thai' => 'dang_on_tap',
                'ngay_tao' => $now->copy()->subDays(10),
                'ngay_cap_nhat' => $now,
            ],
        ];

        DB::table('lich_su_loi_phat_ams')->upsert(
            $lichSuLoiPhatAms,
            ['nguoi_dung_id', 'tu_vung_id', 'loai_loi'],
            ['so_lan_mac_loi', 'lan_mac_loi_gan_nhat', 'chi_tiet_loi', 'trang_thai', 'ngay_cap_nhat']
        );
    }
}
