<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhienLuyenTapSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Giả định: nguoi_dung_id 4..10 (học viên mẫu); bai_hoc_id 1..18 theo BaiHocSeeder trước đó
        $phienLuyenTaps = [
            [
                'id' => 1,
                'nguoi_dung_id' => 4, // Phạm Thị Học
                'bai_hoc_id' => 1, // Nguyên âm đơn: a, ă, â
                'thoi_gian_bat_dau' => $now->copy()->subMinutes(30),
                'thoi_gian_ket_thuc' => $now->copy()->subMinutes(25),
                'tong_diem' => 85,
                'ngay_tao' => $now->copy()->subMinutes(30),
            ],
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'bai_hoc_id' => 4, // Nguyên âm đôi: ai, ao, au
                'thoi_gian_bat_dau' => $now->copy()->subDays(1)->subMinutes(20),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(1)->subMinutes(10),
                'tong_diem' => 72,
                'ngay_tao' => $now->copy()->subDays(1)->subMinutes(20),
            ],
            [
                'id' => 3,
                'nguoi_dung_id' => 7, // Đặng Minh Học
                'bai_hoc_id' => 3, // Từ hàng ngày
                'thoi_gian_bat_dau' => $now->copy()->subDays(2)->subMinutes(15),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(2)->subMinutes(5),
                'tong_diem' => 90,
                'ngay_tao' => $now->copy()->subDays(2)->subMinutes(15),
            ],
            [
                'id' => 4,
                'nguoi_dung_id' => 8, // Hoàng Thị Thảo
                'bai_hoc_id' => 6, // Thanh ngang và huyền
                'thoi_gian_bat_dau' => $now->copy()->subDays(3)->subMinutes(40),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(3)->subMinutes(20),
                'tong_diem' => 98,
                'ngay_tao' => $now->copy()->subDays(3)->subMinutes(40),
            ],
            [
                'id' => 5,
                'nguoi_dung_id' => 9, // Bùi Văn Tài
                'bai_hoc_id' => 8, // Chủ đề: Động vật
                'thoi_gian_bat_dau' => $now->copy()->subHours(5),
                'thoi_gian_ket_thuc' => $now->copy()->subHours(4)->subMinutes(50),
                'tong_diem' => 55,
                'ngay_tao' => $now->copy()->subHours(5),
            ],
            [
                'id' => 6,
                'nguoi_dung_id' => 10, // Trịnh Thị Yêu
                'bai_hoc_id' => 10, // Câu ngắn: chào hỏi
                'thoi_gian_bat_dau' => $now->copy()->subDays(1)->subHours(2),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(1)->subHours(1)->subMinutes(50),
                'tong_diem' => 82,
                'ngay_tao' => $now->copy()->subDays(1)->subHours(2),
            ],
        ];

        DB::table('phien_luyen_taps')->upsert(
            $phienLuyenTaps,
            ['id'],
            ['nguoi_dung_id', 'bai_hoc_id', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'tong_diem', 'ngay_tao']
        );
    }
}
