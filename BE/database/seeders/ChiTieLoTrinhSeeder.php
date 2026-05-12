<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTieLoTrinhSeeder extends Seeder
{
    /**
     * Gắn thứ tự bài học vào từng lộ trình (ghi chú giáo viên cụ thể).
     */
    public function run(): void
    {
        // Lấy danh sách lộ trình cá nhân hiện có
        $loTrinhs = DB::table('lo_trinh_ca_nhans')->orderBy('id')->get();

        $tieuDeUuTien = [
            'Phát âm phụ âm L và N',
            'Luyện dấu hỏi và dấu ngã',
            'Phân biệt âm S và X',
            'Phân biệt âm TR và CH',
            'Từ vựng: Đồ dùng học tập',
            'Từ vựng: Trái cây miền nhiệt đới',
            'Câu chào hỏi lịch sự',
        ];

        $ghiChuMau = [
            'Ưu tiên luyện khẩu hình trước gương — ghi âm lại để so sánh với mẫu.',
            'Sử dụng thẻ hình ảnh (flashcards) để vừa nhận diện mặt chữ vừa phát âm.',
            'Chú ý độ mở của khoang miệng và vị trí đặt lưỡi như trong hướng dẫn.',
            'Thực hành chậm, rõ chữ trước khi tăng tốc độ giao tiếp.',
        ];

        foreach ($loTrinhs as $loTrinh) {
            foreach ($tieuDeUuTien as $thuTu => $tieuDe) {
                // Lấy ID bài học dựa trên tiêu đề đã chuẩn hóa
                $baiId = DB::table('bai_hocs')->where('tieu_de', $tieuDe)->value('id');

                if (!$baiId) {
                    continue;
                }

                DB::table('chi_tiet_lo_trinhs')->updateOrInsert(
                    [
                        'lo_trinh_id' => $loTrinh->id,
                        'bai_hoc_id' => $baiId,
                    ],
                    [
                        'thu_tu_uu_tien' => $thuTu + 1,
                        'ghi_chu_gv' => $ghiChuMau[$thuTu % count($ghiChuMau)],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
