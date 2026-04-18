<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaiTroQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Lấy ID của các vai trò
        $adminId = DB::table('vai_tros')->where('ten_vai_tro', 'admin')->value('id');
        $gvId = DB::table('vai_tros')->where('ten_vai_tro', 'giao_vien')->value('id');
        $hvId = DB::table('vai_tros')->where('ten_vai_tro', 'nguoi_dung')->value('id');

        // 2. Lấy ID của các quyền
        $quyenThemBH = DB::table('quyens')->where('ma_quyen', 'them_bai_hoc')->value('id');
        $quyenSuaBH = DB::table('quyens')->where('ma_quyen', 'sua_bai_hoc')->value('id');
        $quyenXoaBH = DB::table('quyens')->where('ma_quyen', 'xoa_bai_hoc')->value('id');
        $quyenXemBC = DB::table('quyens')->where('ma_quyen', 'xem_bao_cao')->value('id');
        $quyenTaoLT = DB::table('quyens')->where('ma_quyen', 'tao_lo_trinh')->value('id');
        $quyenGhiAm = DB::table('quyens')->where('ma_quyen', 'ghi_am')->value('id');
        $quyenChatAI = DB::table('quyens')->where('ma_quyen', 'chat_ai')->value('id');

        // 3. Định nghĩa mảng phân quyền
        $data = [];

        // ADMIN: Có tất cả các quyền
        $allQuyenIds = DB::table('quyens')->pluck('id');
        foreach ($allQuyenIds as $qId) {
            $data[] = ['vai_tro_id' => $adminId, 'quyen_id' => $qId];
        }

        // GIÁO VIÊN: Quản lý bài học, xem báo cáo, tạo lộ trình, chat AI
        $gvQuyens = [$quyenThemBH, $quyenSuaBH, $quyenXemBC, $quyenTaoLT, $quyenChatAI];
        foreach ($gvQuyens as $qId) {
            $data[] = ['vai_tro_id' => $gvId, 'quyen_id' => $qId];
        }

        // HỌC VIÊN (nguoi_dung): Chỉ được ghi âm và chat AI
        $hvQuyens = [$quyenGhiAm, $quyenChatAI];
        foreach ($hvQuyens as $qId) {
            $data[] = ['vai_tro_id' => $hvId, 'quyen_id' => $qId];
        }

        // 4. Chèn dữ liệu (Sử dụng insertOrIgnore để tránh lỗi nếu chạy lại seeder)
        foreach ($data as $item) {
            DB::table('vai_tro_quyens')->insertOrIgnore($item);
        }
    }
}
