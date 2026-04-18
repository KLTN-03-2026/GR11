<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoiYLuyenTapSeeder extends Seeder
{
    
    public function run(): void
    {
        $gv1 = DB::table('nguoi_dungs')->where('email', 'maiphuong.gv@gmail.com')->value('id');
        $gv2 = DB::table('nguoi_dungs')->where('email', 'thuylinh.gv@gmail.com')->value('id');
        $gv3 = DB::table('nguoi_dungs')->where('email', 'tranphong.gv@gmail.com')->value('id');

        $capHv = DB::table('nguoi_dungs')->whereIn('email', [
            'giabao.student@gmail.com',
            'minhan.hv@gmail.com',
            'thaovy.kid@outlook.com',
            'hoanglong.phuong@gmail.com',
            'khanhlinh.hv@gmail.com',
            'haanh.hv@gmail.com',
            'tunglam.kid@gmail.com',
            'hientrang.hv@gmail.com',
        ])->pluck('id', 'email');

        if (!$gv1 || $capHv->isEmpty()) {
            return;
        }

        DB::table('goi_y_luyen_taps')->whereIn('giao_vien_id', array_filter([$gv1, $gv2, $gv3]))->delete();

        $cacCap = [
            // Giáo viên 1 - Gia Bảo
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('giabao.student@gmail.com'),
                'noi_dung' => 'Tuần này Gia Bảo hãy tập trung vào cặp L/N nhé. Con hãy đọc to các từ "lúa non", "no nê" để thấy sự khác biệt của đầu lưỡi.',
                'da_doc' => 1,
            ],
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('giabao.student@gmail.com'),
                'noi_dung' => 'Nếu điểm chính xác chưa cao, con hãy xem lại video hướng dẫn cách đặt lưỡi của âm TR và CH rồi thử lại lần nữa nhé!',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('giabao.student@gmail.com'),
                'noi_dung' => 'Gia Bảo hãy luyện tập phát âm các từ vựng "Gia đình thân thương" mỗi ngày 10 phút nhé. Phụ huynh có thể nghe và nhận xét cho con.',
                'da_doc' => 0,
            ],
            // Giáo viên 1 - Minh An
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('minhan.hv@gmail.com'),
                'noi_dung' => 'Minh An luyện thêm dấu hỏi và dấu ngã qua các từ: "quả bóng", "vẽ tranh". Con nhớ ghi âm lại để cô nghe giọng con nhé!',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('minhan.hv@gmail.com'),
                'noi_dung' => 'Con đã hoàn thành bài "Từ vựng con vật nuôi" rất tốt. Tiếp theo, con hãy học bài "Các bộ phận cơ thể" để hiểu rõ hơn về cơ thể con người.',
                'da_doc' => 0,
            ],
            // Giáo viên 2 - Thảo Vy
            [
                'giao_vien_id' => $gv2,
                'hoc_vien_id' => $capHv->get('thaovy.kid@outlook.com'),
                'noi_dung' => 'Thảo Vy thực hành âm S/X rất tốt. Con hãy thử tìm các đồ vật trong nhà như "quyển sổ" hay "cái xoong" để luyện phát âm thêm.',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv2,
                'hoc_vien_id' => $capHv->get('thaovy.kid@outlook.com'),
                'noi_dung' => 'Con hãy ôn tập lại bài "Từ vựng trái cây miền nhiệt đới" trước khi vào bài kiểm tra nhé. Đọc thành tiếng và ghi nhớ hình ảnh từng từ.',
                'da_doc' => 0,
            ],
            // Giáo viên 2 - Hoàng Long
            [
                'giao_vien_id' => $gv2,
                'hoc_vien_id' => $capHv->get('hoanglong.phuong@gmail.com'),
                'noi_dung' => 'Hoàng Long hãy tập lấy hơi bụng trước khi đọc bài nhé. Thổi hơi qua ống hút vào nước sẽ giúp hơi của con dài và ổn định hơn đấy.',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv2,
                'hoc_vien_id' => $capHv->get('hoanglong.phuong@gmail.com'),
                'noi_dung' => 'Con cố gắng rất tốt tuần này. Hãy tiếp tục luyện tập câu giao tiếp: "Sáng nay em ngủ tốt không?" để phát âm tự nhiên hơn.',
                'da_doc' => 0,
            ],
            // Giáo viên 1 - Khánh Linh
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('khanhlinh.hv@gmail.com'),
                'noi_dung' => 'Khánh Linh ôn lại từ vựng "Đồ dùng học tập". Phụ huynh nhắc bé phát âm rõ âm cuối như "thước kẻ", "bút mực" nhé.',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv1,
                'hoc_vien_id' => $capHv->get('khanhlinh.hv@gmail.com'),
                'noi_dung' => 'Em làm rất chăm chỉ. Bây giờ hãy học thêm bài "Từ vựng thiên nhiên và mùa" để mở rộng vốn từ vựng hơn.',
                'da_doc' => 0,
            ],
            // Giáo viên 3 - Hà Anh
            [
                'giao_vien_id' => $gv3,
                'hoc_vien_id' => $capHv->get('haanh.hv@gmail.com'),
                'noi_dung' => 'Hà Anh, hôm nay con luyện bài "Phân biệt dấu huyền và dấu sắc" rất tốt. Tiếp tục như vậy nhé! Con sẽ thành thạo phát âm tiếng Việt đấy.',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv3,
                'hoc_vien_id' => $capHv->get('haanh.hv@gmail.com'),
                'noi_dung' => 'Con hãy xem video minh họa về các bộ phận cơ thể rồi học những từ mới. Phát âm rõ mỗi bộ phận một lần để nhớ lâu hơn.',
                'da_doc' => 0,
            ],
            // Giáo viên 3 - Tùng Lâm
            [
                'giao_vien_id' => $gv3,
                'hoc_vien_id' => $capHv->get('tunglam.kid@gmail.com'),
                'noi_dung' => 'Tùng Lâm, em học bài "Từ vựng thế giới động vật" rất chậm. Hãy luyện tập thêm mỗi ngày để chắc chắn nhớ tất cả các từ nhé.',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv3,
                'hoc_vien_id' => $capHv->get('tunglam.kid@gmail.com'),
                'noi_dung' => 'Lần sau khi luyện câu hỏi, hãy hỏi phụ huynh để giúp con phát âm lưu loát hơn. Phụ huynh sẽ là người luyện tập tốt nhất của con.',
                'da_doc' => 0,
            ],
            // Giáo viên 2 - Hiền Trang
            [
                'giao_vien_id' => $gv2,
                'hoc_vien_id' => $capHv->get('hientrang.hv@gmail.com'),
                'noi_dung' => 'Hiền Trang học rất khéo. Tuần này con học thêm bài "Từ vựng thế giới đại dương" để mở rộng kiến thức về những sinh vật biển.',
                'da_doc' => 0,
            ],
            [
                'giao_vien_id' => $gv2,
                'hoc_vien_id' => $capHv->get('hientrang.hv@gmail.com'),
                'noi_dung' => 'Con đã sẵn sàng để kiểm tra "Đánh giá phát âm tổng quát" rồi. Bình tĩnh, tập trung phát âm rõ từng âm tiết nhé!',
                'da_doc' => 0,
            ],
        ];

        foreach ($cacCap as $row) {
            if (empty($row['hoc_vien_id'])) {
                continue;
            }
            DB::table('goi_y_luyen_taps')->insert([
                'giao_vien_id' => $row['giao_vien_id'],
                'hoc_vien_id' => $row['hoc_vien_id'],
                'noi_dung' => $row['noi_dung'],
                'da_doc' => $row['da_doc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
