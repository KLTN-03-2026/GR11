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

        /** bai_hoc_id theo {@see BaiHocSeeder}: 1 Nguyên âm đơn, 4 Phụ âm khó, 3 Phụ âm lưỡi, 6 Sáu thanh, 19 Vật nuôi, 37 Lời chào */
        $phienLuyenTaps = [
            [
                'id' => 1,
                'nguoi_dung_id' => 4,
                'bai_hoc_id' => 1,
                'thoi_gian_bat_dau' => $now->copy()->subMinutes(30),
                'thoi_gian_ket_thuc' => $now->copy()->subMinutes(25),
                'tong_diem' => 88,
                'created_at' => $now->copy()->subMinutes(30),
                'updated_at' => $now->copy()->subMinutes(30),
            ],
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'bai_hoc_id' => 4,
                'thoi_gian_bat_dau' => $now->copy()->subDays(1)->subMinutes(20),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(1)->subMinutes(10),
                'tong_diem' => 72,
                'created_at' => $now->copy()->subDays(1)->subMinutes(20),
                'updated_at' => $now->copy()->subDays(1)->subMinutes(20),
            ],
            [
                'id' => 3,
                'nguoi_dung_id' => 7,
                'bai_hoc_id' => 3,
                'thoi_gian_bat_dau' => $now->copy()->subDays(2)->subMinutes(15),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(2)->subMinutes(5),
                'tong_diem' => 90,
                'created_at' => $now->copy()->subDays(2)->subMinutes(15),
                'updated_at' => $now->copy()->subDays(2)->subMinutes(15),
            ],
            [
                'id' => 4,
                'nguoi_dung_id' => 8,
                'bai_hoc_id' => 6,
                'thoi_gian_bat_dau' => $now->copy()->subDays(3)->subMinutes(40),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(3)->subMinutes(20),
                'tong_diem' => 96,
                'created_at' => $now->copy()->subDays(3)->subMinutes(40),
                'updated_at' => $now->copy()->subDays(3)->subMinutes(40),
            ],
            [
                'id' => 5,
                'nguoi_dung_id' => 9,
                'bai_hoc_id' => 19,
                'thoi_gian_bat_dau' => $now->copy()->subHours(5),
                'thoi_gian_ket_thuc' => $now->copy()->subHours(4)->subMinutes(50),
                'tong_diem' => 58,
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'id' => 6,
                'nguoi_dung_id' => 10,
                'bai_hoc_id' => 37,
                'thoi_gian_bat_dau' => $now->copy()->subDays(1)->subHours(2),
                'thoi_gian_ket_thuc' => $now->copy()->subDays(1)->subHours(1)->subMinutes(50),
                'tong_diem' => 82,
                'created_at' => $now->copy()->subDays(1)->subHours(2),
                'updated_at' => $now->copy()->subDays(1)->subHours(2),
            ],
        ];

        DB::table('phien_luyen_taps')->upsert(
            $phienLuyenTaps,
            ['id'],
            ['nguoi_dung_id', 'bai_hoc_id', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'tong_diem', 'created_at', 'updated_at']
        );
    }
}
