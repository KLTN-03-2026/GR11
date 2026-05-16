<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChiTietLuyenTapSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $tuChuanTheoPhien = [
            1 => TuVungLookup::firstIdsForLesson(1, 3),
            2 => array_values(array_filter([
                TuVungLookup::id(4, 'tre'),
                TuVungLookup::id(4, 'trứng'),
            ])),
            3 => array_values(array_filter([
                TuVungLookup::id(3, 'tủ'),
                TuVungLookup::id(3, 'thỏ'),
                TuVungLookup::id(3, 'lá'),
            ])),
            4 => array_values(array_filter([
                TuVungLookup::id(6, 'la'),
                TuVungLookup::id(6, 'là'),
                TuVungLookup::id(6, 'lá'),
            ])),
            5 => array_values(array_filter([
                TuVungLookup::id(19, 'mèo'),
                TuVungLookup::id(19, 'chó'),
            ])),
            6 => array_values(array_filter([
                TuVungLookup::id(37, 'xin chào'),
            ])),
        ];

        $chiTiet = [];
        $chiId = 1;

        $payloads = [
            1 => [
                ['diem_tin_cay' => 0.98, 'diem_chinh_xac' => 95, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'a'],
                ['diem_tin_cay' => 0.92, 'diem_chinh_xac' => 88, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'ă'],
                ['diem_tin_cay' => 0.90, 'diem_chinh_xac' => 90, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'â'],
            ],
            2 => [
                ['diem_tin_cay' => 0.78, 'diem_chinh_xac' => 72, 'loi_am_dau' => true, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => 'Cần tách rõ trờ–e; tránh đọc gần với chờ.', 'van_ban' => 'tre'],
                ['diem_tin_cay' => 0.81, 'diem_chinh_xac' => 76, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'trứng'],
            ],
            3 => [
                ['diem_tin_cay' => 0.94, 'diem_chinh_xac' => 91, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'tủ'],
                ['diem_tin_cay' => 0.88, 'diem_chinh_xac' => 85, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => true, 'chi_tiet_loi' => 'Thanh hỏi trên vần ỏ cần đủ độ cao.', 'van_ban' => 'thỏ'],
                ['diem_tin_cay' => 0.91, 'diem_chinh_xac' => 89, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'lá'],
            ],
            4 => [
                ['diem_tin_cay' => 0.99, 'diem_chinh_xac' => 98, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'la'],
                ['diem_tin_cay' => 0.96, 'diem_chinh_xac' => 94, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'là'],
                ['diem_tin_cay' => 0.97, 'diem_chinh_xac' => 96, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'lá'],
            ],
            5 => [
                ['diem_tin_cay' => 0.62, 'diem_chinh_xac' => 58, 'loi_am_dau' => true, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => 'Âm mờ–eo cần rõ; tránh nuốt thanh huyền.', 'van_ban' => 'mèo'],
                ['diem_tin_cay' => 0.70, 'diem_chinh_xac' => 65, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => false, 'chi_tiet_loi' => null, 'van_ban' => 'chó'],
            ],
            6 => [
                ['diem_tin_cay' => 0.84, 'diem_chinh_xac' => 83, 'loi_am_dau' => false, 'loi_van' => false, 'loi_thanh_dieu' => true, 'chi_tiet_loi' => 'Ngữ điệu chào hỏi cần tự nhiên hơn ở âm cuối «chào».', 'van_ban' => 'xin chào'],
            ],
        ];

        foreach (array_keys($tuChuanTheoPhien) as $phienId) {
            $tuIds = $tuChuanTheoPhien[$phienId] ?? [];
            $meta = $payloads[$phienId] ?? [];
            foreach ($tuIds as $i => $tuVungId) {
                if (! $tuVungId || ! isset($meta[$i])) {
                    continue;
                }
                $m = $meta[$i];
                $sub = match ($phienId) {
                    1 => $now->copy()->subMinutes(29 - $i),
                    2 => $now->copy()->subDays(1)->subMinutes(19 - $i),
                    3 => $now->copy()->subDays(2)->subMinutes(14 - $i),
                    4 => $now->copy()->subDays(3)->subMinutes(39 - $i),
                    5 => $now->copy()->subHours(5),
                    6 => $now->copy()->subDays(1)->subHours(2),
                    default => $now,
                };

                $chiTiet[] = [
                    'id' => $chiId++,
                    'phien_id' => $phienId,
                    'tu_vung_id' => $tuVungId,
                    'file_ghi_am_url' => null,
                    'van_ban_ai_nhan_dien' => $m['van_ban'],
                    'diem_tin_cay' => $m['diem_tin_cay'],
                    'diem_chinh_xac' => $m['diem_chinh_xac'],
                    'loi_am_dau' => $m['loi_am_dau'],
                    'loi_van' => $m['loi_van'],
                    'loi_thanh_dieu' => $m['loi_thanh_dieu'],
                    'chi_tiet_loi' => $m['chi_tiet_loi'],
                    'created_at' => $sub,
                    'updated_at' => $sub,
                ];
            }
        }

        if ($chiTiet === []) {
            return;
        }

        DB::table('chi_tiet_luyen_taps')->upsert(
            $chiTiet,
            ['id'],
            [
                'phien_id',
                'tu_vung_id',
                'file_ghi_am_url',
                'van_ban_ai_nhan_dien',
                'diem_tin_cay',
                'diem_chinh_xac',
                'loi_am_dau',
                'loi_van',
                'loi_thanh_dieu',
                'chi_tiet_loi',
                'created_at',
                'updated_at',
            ]
        );
    }
}
