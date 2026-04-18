<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaiHocSeeder extends Seeder
{
    /**
     * Bài học đa dạng — gán xen kẽ hai giáo viên seed.
     */
    public function run(): void
    {
        $gv1 = DB::table('nguoi_dungs')->where('email', 'maiphuong.gv@gmail.com')->value('id');
        $gv2 = DB::table('nguoi_dungs')->where('email', 'thuylinh.gv@gmail.com')->value('id');
        $gv3 = DB::table('nguoi_dungs')->where('email', 'tranphong.gv@gmail.com')->value('id');

        $dmAmTiet = DB::table('danh_muc_bai_hocs')->where('ten_danh_muc', 'Luyện phát âm Âm tiết')->value('id');
        $dmTuVung = DB::table('danh_muc_bai_hocs')->where('ten_danh_muc', 'Từ vựng theo Chủ đề')->value('id');
        $dmCau = DB::table('danh_muc_bai_hocs')->where('ten_danh_muc', 'Câu đơn giao tiếp')->value('id');
        $dmKiemTra = DB::table('danh_muc_bai_hocs')->where('ten_danh_muc', 'Kiểm tra định kỳ')->value('id');
        $dmKhoiDong = DB::table('danh_muc_bai_hocs')->where('ten_danh_muc', 'Khởi động giọng nói')->value('id');

        $baihocs = [
            // --- NHÓM ÂM TIẾT ---
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Phát âm phụ âm L và N',
                'mo_ta' => 'Phân biệt âm đầu: L (lưỡi chạm nướu trên, luồng hơi đi qua hai bên) và N (lưỡi chạm nướu trên, hơi đi qua mũi). Ví dụ: lo lắng, nỗ lực.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Luyện dấu hỏi và dấu ngã',
                'mo_ta' => 'Phân biệt thanh điệu: Dấu hỏi (xuống thấp rồi lên nhẹ) và Dấu ngã (lên cao, có quãng ngắt giữa). Ví dụ: củ cải, vẽ tranh.',
                'cap_do' => 'kho',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Các nguyên âm đơn cơ bản',
                'mo_ta' => 'Luyện tập khẩu hình cho các nguyên âm: a, ă, â, e, ê, i, o, ô, ơ, u, ư. Chú ý độ mở của miệng và vị trí đặt lưỡi.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Phân biệt âm S và X',
                'mo_ta' => 'S (âm quặt lưỡi, phát âm mạnh) và X (âm mặt lưỡi, hơi đẩy ra nhẹ nhàng). Ví dụ: phù sa, xa xôi.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Phân biệt âm TR và CH',
                'mo_ta' => 'TR (đầu lưỡi quặt lên vòm cứng) và CH (mặt lưỡi áp vào vòm miệng). Ví dụ: tre xanh, cha con.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Phụ âm cuối -n và -ng',
                'mo_ta' => 'Luyện phát âm kết thúc từ: -n (đầu lưỡi chạm nướu) và -ng (gốc lưỡi chạm vòm mềm). Ví dụ: con chồn, con công.',
                'cap_do' => 'kho',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Phân biệt dấu huyền và dấu sắc',
                'mo_ta' => 'Dấu huyền (giọng xuống từ từ) và Dấu sắc (giọng lên bình ổn). Ví dụ: mẹ (huyền) vs mé (sắc).',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Luyện nguyên âm đôi IE/UA/UO',
                'mo_ta' => 'Phát âm chính xác các nguyên âm ghép: IE, IA, UA, UO, UE. Chú ý sự chuyển tiếp mượt giữa hai âm.',
                'cap_do' => 'kho',
            ],
            [
                'danh_muc_id' => $dmAmTiet,
                'tieu_de' => 'Phụ âm đầu D, Đ, GI và G',
                'mo_ta' => 'Phân biệt 4 âm tương tự: D (không có cuộn lưỡi), Đ (có cuộn lưỡi), GI (như Y), G (âm gốc họng). Ví dụ: da, đá, gia, ga.',
                'cap_do' => 'kho',
            ],

            // --- NHÓM KHỞI ĐỘNG ---
            [
                'danh_muc_id' => $dmKhoiDong,
                'tieu_de' => 'Giải phóng cơ hàm và lưỡi',
                'mo_ta' => 'Các bài tập rung môi, đảo lưỡi và phát âm nhanh các âm tiết ngắn để làm nóng bộ máy phát âm.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmKhoiDong,
                'tieu_de' => 'Luyện tập hô hấp và quản lý hơi',
                'mo_ta' => 'Các bài tập thở từ từ, thở khi phát âm, giữ hơi dài. Giúp kiểm soát giọng nói tốt hơn.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmKhoiDong,
                'tieu_de' => 'Giãn cách vòm miệng và lưỡi',
                'mo_ta' => 'Tập gỡ cổ, mở miệng rộng, nâng mềm vòm miệng. Tạo không gian phát âm tốt hơn.',
                'cap_do' => 'de',
            ],

            // --- NHÓM TỪ VỰNG CHỦ ĐỀ ---
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Đồ dùng học tập',
                'mo_ta' => 'Luyện phát âm các từ: quyển vở, bút mực, thước kẻ, bảng đen, phấn trắng, bìa sơn, túi xách.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Gia đình thân thương',
                'mo_ta' => 'Cách gọi tên các thành viên: ông bà, cha mẹ, anh chị, em bé, chú bác, cô dì, cháu gái.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Trái cây miền nhiệt đới',
                'mo_ta' => 'Luyện tập với các từ: xoài cát, sầu riêng, măng cụt, vú sữa, nhãn lồng, vải thiều, dứa ngoại, ổi hồng.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Các loài hoa',
                'mo_ta' => 'Phát âm tên các loài hoa: hoa hồng, hoa cúc, hoa mai, hoa đào, hoa sen, hoa huệ, hoa lan, hoa lily.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Con vật nuôi',
                'mo_ta' => 'Học phát âm các từ: con mèo, chú cún, thỏ trắng, chim vàng, cá vàng, rùa xanh, chuột nhắt.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Thế giới động vật',
                'mo_ta' => 'Luyện tập phát âm: con sư tử, con voi, con gấu, con khỉ, con hươu, con ngựa, con báo.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Thiên nhiên và mùa',
                'mo_ta' => 'Phát âm từ vựng tự nhiên: mùa xuân/hè/thu/đông, cây xanh, hoa hồng, trời nắng, mưa rơi, sao sáng.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Các bộ phận cơ thể',
                'mo_ta' => 'Luyện phát âm: đôi mắt, cái miệng, bàn tay, cái lưỡi, cái mũi, hai tai, mái tóc, bàn chân.',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmTuVung,
                'tieu_de' => 'Từ vựng: Thế giới đại dương',
                'mo_ta' => 'Phát âm từ vựng biển: con sứa, san hô, sao biển, cá voi, cá heo, bạch tuộc, tôm hùm, cua cà.',
                'cap_do' => 'trung_binh',
            ],

            // --- NHÓM CÂU GIAO TIẾP ---
            [
                'danh_muc_id' => $dmCau,
                'tieu_de' => 'Câu chào hỏi lịch sự',
                'mo_ta' => 'Luyện ngữ điệu câu hỏi và câu trần thuật: Cháu chào ông ạ! / Bạn khỏe không? / Sáng nay em ngủ tốt không?',
                'cap_do' => 'de',
            ],
            [
                'danh_muc_id' => $dmCau,
                'tieu_de' => 'Bày tỏ lòng biết ơn và xin lỗi',
                'mo_ta' => 'Luyện âm điệu chân thành: Con cảm ơn mẹ. / Em xin lỗi thầy vì đi học muộn. / Dạ, con hiểu rồi.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmCau,
                'tieu_de' => 'Yêu cầu và xin phép',
                'mo_ta' => 'Phát âm lễ phép: Thầy ơi, con xin phép! / Mẹ ơi, cho con... được không ạ? / Em muốn hỏi một chút.',
                'cap_do' => 'trung_binh',
            ],
            [
                'danh_muc_id' => $dmCau,
                'tieu_de' => 'Đặt câu hỏi và trả lời cơ bản',
                'mo_ta' => 'Luyện tập: Bé tên gì? / Bé bao nhiêu tuổi? / Bé có mấy anh chị em? / Tiếng Việt là ngôn ngữ của ai?',
                'cap_do' => 'trung_binh',
            ],

            // --- KIỂM TRA ---
            [
                'danh_muc_id' => $dmKiemTra,
                'tieu_de' => 'Đánh giá phát âm tổng quát',
                'mo_ta' => 'Kiểm tra khả năng phân biệt phụ âm đầu, vần khó và cách ngắt nghỉ trong câu dài.',
                'cap_do' => 'kho',
            ],
            [
                'danh_muc_id' => $dmKiemTra,
                'tieu_de' => 'Kiểm tra tiếp theo: Từ vựng nâng cao',
                'mo_ta' => 'Ôn tập từ vựng chủ đề, phát âm chính xác các từ khó, nhớ hình ảnh từng từ.',
                'cap_do' => 'trung_binh',
            ],
        ];

        foreach ($baihocs as $index => $bh) {
            $gvId = ($index % 3 === 0) ? $gv1 : (($index % 3 === 1) ? $gv2 : $gv3);
            DB::table('bai_hocs')->updateOrInsert(
                ['tieu_de' => $bh['tieu_de']],
                [
                    'danh_muc_id' => $bh['danh_muc_id'],
                    'nguoi_tao_id' => $gvId,
                    'mo_ta' => $bh['mo_ta'],
                    'cap_do' => $bh['cap_do'],
                    'thu_tu' => $index + 1,
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
