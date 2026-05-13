<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoTrinhTraPhiSeeder extends Seeder
{
    public function run(): void
    {
        $loTrinhIds = DB::table('lo_trinh_ca_nhans')
            ->where('ten_lo_trinh', 'Lộ trình cơ bản')
            ->orderBy('id')
            ->limit(3)
            ->pluck('id');

        if ($loTrinhIds->isEmpty()) {
            return;
        }

        $now = now();
        $moTa = [
            'Gói lộ trình «cơ bản»: ưu tiên nguyên âm, phụ âm môi và phụ âm lưỡi — phù hợp học viên mới.',
            'Đồng bộ với bài «Phụ âm khó» và «Sáu thanh điệu»; có báo cáo tiến độ hàng tuần.',
            'Kèm gợi ý luyện tập theo lỗi đánh dấu (điểm danh lỗi) của từng học viên.',
        ];

        $rows = [];
        foreach ($loTrinhIds as $i => $loTrinhId) {
            $rows[] = [
                'lo_trinh_id' => $loTrinhId,
                'gia' => 149000,
                'mo_ta_ban' => $moTa[$i] ?? 'Lộ trình phát âm do giáo viên biên soạn.',
                'trang_thai' => 1,
                'ngay_duyet' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('lo_trinh_tra_phis')->upsert(
            $rows,
            ['lo_trinh_id'],
            ['gia', 'mo_ta_ban', 'trang_thai', 'ngay_duyet', 'updated_at']
        );
    }
}
