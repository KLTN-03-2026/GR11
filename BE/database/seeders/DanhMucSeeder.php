<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DanhMucSeeder extends Seeder
{
    /**
     * Danh mục bài học — dữ liệu cố định, tái chạy an toàn (updateOrInsert theo tên).
     */
    public function run(): void
    {
        $danhmucs = [
            [
                'ten_danh_muc' => 'Luyện phát âm Âm tiết',
                'mo_ta' => 'Nguyên âm — phụ âm đầu — cuối: L/N, S/X, TR/CH, dấu thanh. Phù hợp trẻ 4–8 tuổi.',
            ],
            [
                'ten_danh_muc' => 'Từ vựng theo Chủ đề',
                'mo_ta' => 'Từ vựng quanh nhà, trường học, thiên nhiên qua hình ảnh minh họa rõ nét.',
            ],
            [
                'ten_danh_muc' => 'Câu đơn giao tiếp',
                'mo_ta' => 'Mẫu câu ngắn lịch sự: chào, cảm ơn, xin phép — đọc trôi chảy từng nhịp.',
            ],
            [
                'ten_danh_muc' => 'Kiểm tra định kỳ',
                'mo_ta' => 'Ôn tập tổng hợp, đánh giá tiến độ sau mỗi giai đoạn.',
            ],
            [
                'ten_danh_muc' => 'Khởi động giọng nói',
                'mo_ta' => 'Bài khởi động hô hấp — vần điệu nhẹ nhàng trước khi vào bài chính.',
            ],
        ];

        foreach ($danhmucs as $index => $dm) {
            DB::table('danh_muc_bai_hocs')->updateOrInsert(
                ['ten_danh_muc' => $dm['ten_danh_muc']],
                [
                    'slug_danh_muc' => Str::slug($dm['ten_danh_muc']),
                    'mo_ta' => $dm['mo_ta'],
                    'trang_thai' => 0,
                    'thu_tu' => $index + 1,
                    'ngay_tao' => now(),
                ]
            );
        }
    }
}
