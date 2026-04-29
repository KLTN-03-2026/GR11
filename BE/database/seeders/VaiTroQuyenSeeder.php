<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VaiTroQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Ánh xạ quyền cho 3 vai trò: 1=Admin, 2=Giáo viên, 3=Sinh viên
        $vaiTroQuyens = [
            // --- Admin: có hầu hết quyền (1..28)
            // Gán tất cả quyền để Admin quản trị toàn bộ hệ thống
        ];

        // Thêm quyền cho Admin (1..28)
        for ($q = 1; $q <= 28; $q++) {
            $vaiTroQuyens[] = [
                'vai_tro_id' => 1,
                'quyen_id' => $q,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // --- Giáo viên: quyền liên quan đến quản lý nội dung giảng dạy, lộ trình, phiên luyện tập, chấm điểm, quan hệ GV-HV
        $quyenGiaoVien = [
            12, // Tạo khóa học
            13, // Xem khóa học
            14, // Sửa khóa học
            16, // Quản lý từ vựng (thêm/sửa/xóa)
            17, // Quản lý lộ trình cá nhân
            18, // Quản lý quan hệ giáo viên - học viên
            19, // Tạo phiên luyện tập
            20, // Lưu chi tiết luyện tập / kết quả AI
            21, // Tải lên / quản lý file âm thanh
            10, // Quản lý điểm / chấm điểm (nếu id 10 tồn tại trong seed quyens)
            22, // Xem báo cáo tiến độ học viên
        ];

        foreach ($quyenGiaoVien as $qid) {
            $vaiTroQuyens[] = [
                'vai_tro_id' => 2,
                'quyen_id' => $qid,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // --- Sinh viên: quyền hạn chế, chủ yếu xem và tương tác với phiên/khóa học của mình
        $quyenSinhVien = [
            13, // Xem khóa học
            19, // Tạo phiên luyện tập (bắt đầu bài)
            20, // Lưu chi tiết luyện tập / kết quả AI (lưu kết quả của chính họ)
            21, // Tải lên / quản lý file âm thanh (upload file ghi âm)
            22, // Xem báo cáo tiến độ học viên
            26, // Xem dashboard admin (nếu muốn cho dashboard người dùng, có thể bỏ nếu chỉ admin)
        ];

        foreach ($quyenSinhVien as $qid) {
            $vaiTroQuyens[] = [
                'vai_tro_id' => 3,
                'quyen_id' => $qid,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Upsert: dùng ['vai_tro_id','quyen_id'] làm unique key để tránh duplicate
        DB::table('vai_tro_quyens')->upsert(
            $vaiTroQuyens,
            ['vai_tro_id', 'quyen_id'],
            ['updated_at']
        );
    }
}
