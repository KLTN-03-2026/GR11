<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DanhMucSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $danhMucBaiHocs = [
            // 3 danh mục cơ bản, dễ tiếp cận cho mọi đối tượng (bao gồm guest)
            [
                'id' => 1,
                'ten_danh_muc' => 'Âm cơ bản',
                'slug_danh_muc' => Str::slug('Âm cơ bản'),
                'mo_ta' => 'Giới thiệu nguyên âm và phụ âm đơn giản; bài tập phát âm từng âm riêng lẻ, phù hợp cho trẻ nhỏ và guest.',
                'trang_thai' => 1,
                'thu_tu' => 1,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'ten_danh_muc' => 'Âm ghép đơn giản',
                'slug_danh_muc' => Str::slug('Âm ghép đơn giản'),
                'mo_ta' => 'Kết hợp nguyên âm và phụ âm tạo từ ngắn; nhiều bài tập minh họa bằng hình ảnh và âm thanh.',
                'trang_thai' => 1,
                'thu_tu' => 2,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'ten_danh_muc' => 'Từ và âm thanh quen thuộc',
                'slug_danh_muc' => Str::slug('Từ và âm thanh quen thuộc'),
                'mo_ta' => 'Từ vựng hàng ngày, hành động và đồ vật quen thuộc để liên kết âm với ngữ cảnh thực tế.',
                'trang_thai' => 1,
                'thu_tu' => 3,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Các danh mục nâng dần độ khó, phù hợp cho trẻ em có rối loạn phát âm
            [
                'id' => 4,
                'ten_danh_muc' => 'Âm đôi và phụ âm khó',
                'slug_danh_muc' => Str::slug('Âm đôi và phụ âm khó'),
                'mo_ta' => 'Luyện các nguyên âm đôi (ai, ao, eo...) và phụ âm khó (tr, ch, r, s, ng), kèm bài tập phân biệt.',
                'trang_thai' => 1,
                'thu_tu' => 4,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'ten_danh_muc' => 'Thanh điệu tiếng Việt',
                'slug_danh_muc' => Str::slug('Thanh điệu tiếng Việt'),
                'mo_ta' => 'Luyện 6 thanh điệu; bài tập phân biệt từ cùng âm khác thanh, kèm ví dụ trực quan.',
                'trang_thai' => 1,
                'thu_tu' => 5,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'ten_danh_muc' => 'Từ vựng theo chủ đề',
                'slug_danh_muc' => Str::slug('Từ vựng theo chủ đề'),
                'mo_ta' => 'Từ vựng theo chủ đề (gia đình, trường học, động vật, màu sắc) để luyện phát âm trong ngữ cảnh.',
                'trang_thai' => 1,
                'thu_tu' => 6,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'ten_danh_muc' => 'Câu giao tiếp ngắn',
                'slug_danh_muc' => Str::slug('Câu giao tiếp ngắn'),
                'mo_ta' => 'Mẫu câu đơn giản (chào hỏi, xin phép, cảm ơn) để luyện phát âm trong tình huống thực tế.',
                'trang_thai' => 1,
                'thu_tu' => 7,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'ten_danh_muc' => 'Ngữ điệu và nhịp điệu',
                'slug_danh_muc' => Str::slug('Ngữ điệu và nhịp điệu'),
                'mo_ta' => 'Luyện lên xuống giọng, ngắt nghỉ và nhịp câu để nói tự nhiên và dễ hiểu hơn.',
                'trang_thai' => 1,
                'thu_tu' => 8,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'ten_danh_muc' => 'Ôn tập và kiểm tra tiến bộ',
                'slug_danh_muc' => Str::slug('Ôn tập và kiểm tra tiến bộ'),
                'mo_ta' => 'Bài tập tổng hợp, kiểm tra định kỳ và báo cáo tiến bộ; phù hợp cho giáo viên và phụ huynh theo dõi.',
                'trang_thai' => 1,
                'thu_tu' => 9,
                'ngay_tao' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('danh_muc_bai_hocs')->upsert(
            $danhMucBaiHocs,
            ['id'],
            ['ten_danh_muc', 'slug_danh_muc', 'mo_ta', 'trang_thai', 'thu_tu', 'updated_at']
        );
    }
}
