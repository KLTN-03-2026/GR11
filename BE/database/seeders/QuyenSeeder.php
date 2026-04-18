<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quyens = [
            // Quản lý nội dung
            ['ten_quyen' => 'Thêm bài học', 'ma_quyen' => 'them_bai_hoc'],
            ['ten_quyen' => 'Sửa bài học', 'ma_quyen' => 'sua_bai_hoc'],
            ['ten_quyen' => 'Xóa bài học', 'ma_quyen' => 'xoa_bai_hoc'],

            // Quản lý học viên
            ['ten_quyen' => 'Xem báo cáo học viên', 'ma_quyen' => 'xem_bao_cao'],
            ['ten_quyen' => 'Tạo lộ trình cá nhân', 'ma_quyen' => 'tao_lo_trinh'],

            // Chức năng học tập
            ['ten_quyen' => 'Ghi âm luyện tập', 'ma_quyen' => 'ghi_am'],
            ['ten_quyen' => 'Sử dụng AI Chatbox', 'ma_quyen' => 'chat_ai'],
        ];
        foreach ($quyens as $q) {
            DB::table('quyens')->updateOrInsert(['ma_quyen' => $q['ma_quyen']], $q);
        }
    }
}
