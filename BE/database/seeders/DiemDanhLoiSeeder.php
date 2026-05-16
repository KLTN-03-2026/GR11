<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiemDanhLoiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $defs = [
            ['id' => 1, 'nguoi_dung_id' => 4, 'bai_hoc_id' => 4, 'tu_chuan' => 'tre', 'muc_do_uu_tien' => 'cao', 'ghi_chu' => 'Khó tách tr–ch; ưu tiên luyện cặp «tre / chó».', 'da_hoan_thanh' => false, 'sub_days' => 10],
            ['id' => 2, 'nguoi_dung_id' => 4, 'bai_hoc_id' => 4, 'tu_chuan' => 'chuối', 'muc_do_uu_tien' => 'binh_thuong', 'ghi_chu' => 'Âm ch–uôi cần kéo dài vần đủ.', 'da_hoan_thanh' => true, 'sub_days' => 20],
            ['id' => 3, 'nguoi_dung_id' => 5, 'bai_hoc_id' => 3, 'tu_chuan' => 'tủ', 'muc_do_uu_tien' => 'thap', 'ghi_chu' => 'Mới đánh dấu; chưa luyện đều.', 'da_hoan_thanh' => false, 'sub_days' => 2],
            ['id' => 4, 'nguoi_dung_id' => 6, 'bai_hoc_id' => 4, 'tu_chuan' => 'rổ', 'muc_do_uu_tien' => 'cao', 'ghi_chu' => 'Âm r rung; so sánh với d/g cùng vị trí.', 'da_hoan_thanh' => false, 'sub_days' => 30],
            ['id' => 5, 'nguoi_dung_id' => 7, 'bai_hoc_id' => 2, 'tu_chuan' => 'mẹ', 'muc_do_uu_tien' => 'binh_thuong', 'ghi_chu' => 'Thanh nặng trên vần e ổn định sau ôn.', 'da_hoan_thanh' => true, 'sub_days' => 7],
            ['id' => 6, 'nguoi_dung_id' => 8, 'bai_hoc_id' => 6, 'tu_chuan' => 'la', 'muc_do_uu_tien' => 'binh_thuong', 'ghi_chu' => 'Dùng «la» làm mốc cho dãy 6 thanh.', 'da_hoan_thanh' => true, 'sub_days' => 15],
            ['id' => 7, 'nguoi_dung_id' => 9, 'bai_hoc_id' => 19, 'tu_chuan' => 'mèo', 'muc_do_uu_tien' => 'thap', 'ghi_chu' => 'Theo dõi sau phiên vật nuôi trong nhà.', 'da_hoan_thanh' => false, 'sub_days' => 1],
            ['id' => 8, 'nguoi_dung_id' => 10, 'bai_hoc_id' => 37, 'tu_chuan' => 'xin chào', 'muc_do_uu_tien' => 'cao', 'ghi_chu' => 'Ngữ điệu cụm chào cần tự nhiên hơn.', 'da_hoan_thanh' => false, 'sub_days' => 4],
        ];

        $rows = [];
        foreach ($defs as $d) {
            $tid = TuVungLookup::id((int) $d['bai_hoc_id'], (string) $d['tu_chuan']);
            if (! $tid) {
                continue;
            }
            $start = $now->copy()->subDays((int) $d['sub_days']);
            $rows[] = [
                'id' => $d['id'],
                'nguoi_dung_id' => $d['nguoi_dung_id'],
                'tu_vung_id' => $tid,
                'muc_do_uu_tien' => $d['muc_do_uu_tien'],
                'ghi_chu' => $d['ghi_chu'],
                'da_hoan_thanh' => $d['da_hoan_thanh'],
                'ngay_danh_dau' => $start,
                'ngay_hoan_thanh' => $d['da_hoan_thanh'] ? $start->copy()->addDays(5) : null,
                'created_at' => $start,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return;
        }

        DB::table('diem_danh_lois')->upsert(
            $rows,
            ['id'],
            ['nguoi_dung_id', 'tu_vung_id', 'muc_do_uu_tien', 'ghi_chu', 'da_hoan_thanh', 'ngay_danh_dau', 'ngay_hoan_thanh', 'updated_at']
        );
    }
}
