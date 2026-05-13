<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoiYLuyenTapSeeder extends Seeder
{
    public function run(): void
    {
        $gvIds = DB::table('nguoi_dungs')->where('vai_tro_id', 2)->orderBy('id')->pluck('id')->take(2)->all();
        if (count($gvIds) < 2) {
            return;
        }
        [$gv1, $gv2] = [$gvIds[0], $gvIds[1]];

        $hv4 = DB::table('nguoi_dungs')->where('email', 'phamlananh@gmail.com')->value('id');
        $hv7 = DB::table('nguoi_dungs')->where('email', 'dangminhkhoi@gmail.com')->value('id');
        $hv8 = DB::table('nguoi_dungs')->where('email', 'hoangthithao@gmail.com')->value('id');
        $hv9 = DB::table('nguoi_dungs')->where('email', 'buivantai@gmail.com')->value('id');

        foreach (array_filter([$hv4, $hv7, $hv8, $hv9]) as $hid) {
            DB::table('goi_y_luyen_taps')->where('hoc_vien_id', $hid)->delete();
        }

        $now = now();
        $rows = [];

        if ($hv4) {
            $rows[] = ['giao_vien_id' => $gv1, 'hoc_vien_id' => $hv4, 'noi_dung' => 'Lan Anh ôn lại các nguyên âm đơn (a, ă, â…) theo đúng phiên âm mẫu trong bài «Nguyên âm đơn»; mỗi âm đọc chậm 3 lần trước khi ghép vần.', 'da_doc' => 1];
            $rows[] = ['giao_vien_id' => $gv1, 'hoc_vien_id' => $hv4, 'noi_dung' => 'Tuần này luyện thêm cặp tre / chó trong bài «Phụ âm khó»: chú ý vị trí lưỡi quặt so với âm mặt lưỡi.', 'da_doc' => 0];
        }
        if ($hv7) {
            $rows[] = ['giao_vien_id' => $gv2, 'hoc_vien_id' => $hv7, 'noi_dung' => 'Minh Khôi luyện dãy thanh la / là / lá để phân biệt 6 thanh; ghi âm từng từ và so với mẫu hệ thống.', 'da_doc' => 0];
        }
        if ($hv8) {
            $rows[] = ['giao_vien_id' => $gv2, 'hoc_vien_id' => $hv8, 'noi_dung' => 'Thảo thử đọc chuỗi «xin chào — tạm biệt» trong bài chào hỏi; giữ hơi đều, không nuốt âm cuối.', 'da_doc' => 0];
        }
        if ($hv9) {
            $rows[] = ['giao_vien_id' => $gv1, 'hoc_vien_id' => $hv9, 'noi_dung' => 'Văn Tài tập «mèo / chó / gà» ở bài vật nuôi: nhấn rõ âm đầu mờ, chờ, gờ.', 'da_doc' => 0];
        }

        foreach ($rows as $r) {
            DB::table('goi_y_luyen_taps')->insert([
                'giao_vien_id' => $r['giao_vien_id'],
                'hoc_vien_id' => $r['hoc_vien_id'],
                'noi_dung' => $r['noi_dung'],
                'da_doc' => $r['da_doc'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
