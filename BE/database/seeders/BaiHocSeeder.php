<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BaiHocSeeder extends Seeder
{
    /**
     * Bài học đa dạng — gán xen kẽ hai giáo viên seed.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Giả định: danh_muc_id 1..9 đã tồn tại; nguoi_tao_id 2 và 3 là giáo viên
        $baiHocs = [
            // --- 3 bài đầu cơ bản, truy cập cho mọi người (bao gồm guest)
            [
                'id' => 1,
                'danh_muc_id' => 1, // Âm cơ bản
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Nguyên âm đơn: a, ă, â',
                'mo_ta' => 'Giới thiệu và luyện phát âm các nguyên âm cơ bản a, ă, â; bài tập nghe - lặp lại.',
                'cap_do' => 'basic',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'danh_muc_id' => 1, // Âm cơ bản
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Phụ âm đơn: b, m, p',
                'mo_ta' => 'Luyện phát âm phụ âm dễ nhận biết; hướng dẫn đặt môi và lưỡi.',
                'cap_do' => 'basic',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'danh_muc_id' => 3, // Từ và âm thanh quen thuộc
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Từ hàng ngày: mẹ, ba, ăn, chơi',
                'mo_ta' => 'Từ vựng quen thuộc kèm hình ảnh và âm mẫu để liên kết âm với ngữ cảnh.',
                'cap_do' => 'basic',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Trung cấp: tăng dần độ khó
            [
                'id' => 4,
                'danh_muc_id' => 4, // Âm đôi và phụ âm khó
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Nguyên âm đôi: ai, ao, au',
                'mo_ta' => 'Luyện phát âm nguyên âm đôi với ví dụ từ và bài tập phân biệt.',
                'cap_do' => 'intermediate',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'danh_muc_id' => 4,
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Phụ âm khó: tr, ch, r',
                'mo_ta' => 'Hướng dẫn kỹ thuật phát âm tr, ch, r; bài tập phân biệt âm dễ nhầm.',
                'cap_do' => 'intermediate',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'danh_muc_id' => 5, // Thanh điệu tiếng Việt
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Thanh ngang và thanh huyền: phân biệt cơ bản',
                'mo_ta' => 'Bài tập nghe - lặp lại để phân biệt thanh ngang và huyền trên cùng một âm.',
                'cap_do' => 'intermediate',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'danh_muc_id' => 6, // Từ vựng theo chủ đề
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Chủ đề: Gia đình (mẹ, cha, anh, chị)',
                'mo_ta' => 'Từ vựng theo chủ đề gia đình, kèm bài tập phát âm và ghép câu đơn giản.',
                'cap_do' => 'intermediate',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'danh_muc_id' => 6,
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Chủ đề: Động vật (mèo, chó, gà)',
                'mo_ta' => 'Từ vựng động vật kèm âm mẫu và hình ảnh minh họa cho trẻ dễ nhớ.',
                'cap_do' => 'intermediate',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Nâng cao: thanh điệu, ngữ điệu, câu
            [
                'id' => 9,
                'danh_muc_id' => 5, // Thanh điệu tiếng Việt
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Thanh hỏi, ngã và nặng: luyện nâng cao',
                'mo_ta' => 'Bài tập nâng cao giúp trẻ nhận diện và phát âm chính xác các thanh khó.',
                'cap_do' => 'advanced',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'danh_muc_id' => 7, // Câu giao tiếp ngắn
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Câu ngắn: chào hỏi và giới thiệu bản thân',
                'mo_ta' => 'Luyện phát âm trong mẫu câu, chú trọng ngữ điệu và nhịp câu.',
                'cap_do' => 'advanced',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'danh_muc_id' => 8, // Ngữ điệu và nhịp điệu
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Ngữ điệu: câu hỏi và câu khẳng định',
                'mo_ta' => 'Bài tập luyện ngữ điệu để phân biệt câu hỏi và câu khẳng định, cải thiện tính tự nhiên khi nói.',
                'cap_do' => 'advanced',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Bài chuyên sâu cho rối loạn phát âm
            [
                'id' => 12,
                'danh_muc_id' => 4, // Âm đôi và phụ âm khó
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Bài tập phân biệt r/gi/tr cho trẻ gặp khó khăn',
                'mo_ta' => 'Chuỗi bài tập chuyên biệt, hướng dẫn từng bước để phân biệt r, gi, tr.',
                'cap_do' => 'advanced',
                'thu_tu' => 3,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'danh_muc_id' => 8, // Ngữ điệu và nhịp điệu
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Nhịp câu và ngắt nghỉ cho trẻ chậm phát triển ngôn ngữ',
                'mo_ta' => 'Bài tập nhịp điệu, ngắt nghỉ, kết hợp vận động tay để hỗ trợ phát âm.',
                'cap_do' => 'advanced',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Ôn tập và kiểm tra tiến bộ
            [
                'id' => 14,
                'danh_muc_id' => 9, // Ôn tập và kiểm tra tiến bộ
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Bài kiểm tra cơ bản: âm và từ',
                'mo_ta' => 'Bài kiểm tra tổng hợp các âm và từ cơ bản để đánh giá tiến bộ ban đầu.',
                'cap_do' => 'basic',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'danh_muc_id' => 9,
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Bài kiểm tra nâng cao: thanh điệu và ngữ điệu',
                'mo_ta' => 'Bài kiểm tra đánh giá khả năng phân biệt thanh và ngữ điệu sau quá trình luyện tập.',
                'cap_do' => 'advanced',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Bài bổ trợ: âm thanh mẫu và ghi âm
            [
                'id' => 16,
                'danh_muc_id' => 2, // Âm ghép đơn giản
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Ghép âm: ba, be, bi, bo, bu (bài nghe và lặp lại)',
                'mo_ta' => 'Chuỗi từ ghép âm kèm file âm mẫu; có bài tập ghi âm và so sánh.',
                'cap_do' => 'basic',
                'thu_tu' => 1,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'danh_muc_id' => 2,
                'nguoi_tao_id' => 3,
                'tieu_de' => 'Ghép âm: ca, co, cu, câ, cươ (bài luyện cho âm c/k)',
                'mo_ta' => 'Bài tập ghép âm và phân biệt c/k; phù hợp cho trẻ nhầm lẫn âm đầu.',
                'cap_do' => 'intermediate',
                'thu_tu' => 2,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'danh_muc_id' => 6, // Từ vựng theo chủ đề
                'nguoi_tao_id' => 2,
                'tieu_de' => 'Chủ đề: Trường học (bút, sách, bảng)',
                'mo_ta' => 'Từ vựng theo chủ đề trường học, kèm bài tập phát âm và ghép câu ngắn.',
                'cap_do' => 'intermediate',
                'thu_tu' => 3,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('bai_hocs')->upsert(
            $baiHocs,
            ['id'],
            ['danh_muc_id', 'nguoi_tao_id', 'tieu_de', 'mo_ta', 'cap_do', 'thu_tu', 'trang_thai', 'updated_at']
        );
    }
}
