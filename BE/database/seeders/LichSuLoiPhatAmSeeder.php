<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LichSuLoiPhatAmSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = Carbon::now();

        $defs = [
            ['nguoi_dung_id' => 4, 'bai_hoc_id' => 4, 'tu_chuan' => 'tre', 'loai_loi' => 'am_dau', 'so_lan' => 5, 'trang_thai' => 0, 'sub' => 1, 'chi_tiet' => 'Hay lẫn với âm «ch» cùng vị trí; cần bài tập đặt lưỡi.'],
            ['nguoi_dung_id' => 4, 'bai_hoc_id' => 4, 'tu_chuan' => 'chuối', 'loai_loi' => 'am_dau', 'so_lan' => 2, 'trang_thai' => 1, 'sub' => 5, 'chi_tiet' => 'Đã cải thiện sau ôn vần uôi.'],
            ['nguoi_dung_id' => 5, 'bai_hoc_id' => 3, 'tu_chuan' => 'tủ', 'loai_loi' => 'van', 'so_lan' => 1, 'trang_thai' => 0, 'sub' => 2, 'chi_tiet' => 'Vần ư–u cần phân biệt rõ hơn.'],
            ['nguoi_dung_id' => 6, 'bai_hoc_id' => 4, 'tu_chuan' => 'rổ', 'loai_loi' => 'am_dau', 'so_lan' => 8, 'trang_thai' => 1, 'sub' => 1, 'chi_tiet' => 'R rung; tránh đọc gần với d/g.'],
            ['nguoi_dung_id' => 7, 'bai_hoc_id' => 2, 'tu_chuan' => 'mẹ', 'loai_loi' => 'thanh_dieu', 'so_lan' => 3, 'trang_thai' => 2, 'sub' => 3, 'chi_tiet' => 'Thanh nặng ổn định trong câu ngắn.'],
            ['nguoi_dung_id' => 8, 'bai_hoc_id' => 6, 'tu_chuan' => 'la', 'loai_loi' => 'thanh_dieu', 'so_lan' => 1, 'trang_thai' => 1, 'sub' => 12, 'chi_tiet' => 'Ngang làm chuẩn so với là, lá.'],
            ['nguoi_dung_id' => 9, 'bai_hoc_id' => 19, 'tu_chuan' => 'mèo', 'loai_loi' => 'am_dau', 'so_lan' => 2, 'trang_thai' => 0, 'sub' => 1, 'chi_tiet' => 'Âm mờ cần rõ hơn khi ghép eo.'],
            ['nguoi_dung_id' => 10, 'bai_hoc_id' => 37, 'tu_chuan' => 'xin chào', 'loai_loi' => 'ngu_dieu', 'so_lan' => 4, 'trang_thai' => 1, 'sub' => 4, 'chi_tiet' => 'Ngữ điệu cụm chào chưa tự nhiên.'],
            ['nguoi_dung_id' => 8, 'bai_hoc_id' => 6, 'tu_chuan' => 'là', 'loai_loi' => 'thanh_dieu', 'so_lan' => 2, 'trang_thai' => 1, 'sub' => 2, 'chi_tiet' => 'Huyền trong chuỗi la–là–lá.'],
        ];

        $rows = [];
        foreach ($defs as $d) {
            $tid = TuVungLookup::id((int) $d['bai_hoc_id'], (string) $d['tu_chuan']);
            if (! $tid) {
                continue;
            }
            $lan = $now->copy()->subDays((int) $d['sub']);
            $rows[] = [
                'nguoi_dung_id' => $d['nguoi_dung_id'],
                'tu_vung_id' => $tid,
                'phien_id' => null,
                'loai_loi' => $d['loai_loi'],
                'so_lan_mac_loi' => $d['so_lan'],
                'lan_mac_loi_gan_nhat' => $lan,
                'chi_tiet_loi' => $d['chi_tiet'],
                'trang_thai' => $d['trang_thai'],
                'created_at' => $lan,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return;
        }

        DB::table('lich_su_loi_phat_ams')->upsert(
            $rows,
            ['nguoi_dung_id', 'tu_vung_id', 'loai_loi'],
            ['so_lan_mac_loi', 'lan_mac_loi_gan_nhat', 'chi_tiet_loi', 'trang_thai', 'phien_id', 'updated_at']
        );
    }
}
