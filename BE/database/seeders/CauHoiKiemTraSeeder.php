<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Câu hỏi gắn với {@see BaiKiemTraSeeder}; phần phát âm tra {@see TuVungLookup} (bai_hoc_id = 1).
 */
class CauHoiKiemTraSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = DB::table('bai_kiem_tras')->where('bai_hoc_id', 1)->first();
        if (! $quiz) {
            return;
        }

        $bid = (int) $quiz->id;

        DB::table('cau_hoi_kiem_tras')->where('bai_kiem_tra_id', $bid)->delete();

        $idA = TuVungLookup::id(1, 'a');
        $idAm = TuVungLookup::id(1, 'â');
        $idE = TuVungLookup::id(1, 'ê');
        $idOhorn = TuVungLookup::id(1, 'ơ');
        if (! $idA || ! $idAm || ! $idE || ! $idOhorn) {
            return;
        }

        $now = Carbon::now();

        $rows = [
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => null,
                'loai' => 'mcq',
                'thu_tu' => 1,
                'noi_dung_cau' => 'Theo từ vựng bài «Nguyên âm đơn», mặt chữ nào có phiên âm mẫu là «ớ»?',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => null,
                'loai' => 'mcq',
                'thu_tu' => 2,
                'noi_dung_cau' => 'Mặt chữ nào có phiên âm mẫu là «á» (ghi theo bảng từ vựng bài học)?',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => null,
                'loai' => 'mcq',
                'thu_tu' => 3,
                'noi_dung_cau' => 'Phiên âm mẫu «ư» (một ký tự) tương ứng mặt chữ nào trong bài?',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => null,
                'loai' => 'mcq',
                'thu_tu' => 4,
                'noi_dung_cau' => 'Mặt chữ nào có phiên âm mẫu đúng là «ô» (theo bản ghi từ vựng)?',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => $idA,
                'loai' => 'phat_am',
                'thu_tu' => 5,
                'noi_dung_cau' => 'Phát âm đúng nguyên âm đơn (mặt chữ: a).',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => $idAm,
                'loai' => 'phat_am',
                'thu_tu' => 6,
                'noi_dung_cau' => 'Phát âm đúng nguyên âm đơn (mặt chữ: â).',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => $idE,
                'loai' => 'phat_am',
                'thu_tu' => 7,
                'noi_dung_cau' => 'Phát âm đúng nguyên âm đơn (mặt chữ: ê).',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bai_kiem_tra_id' => $bid,
                'tu_vung_id' => $idOhorn,
                'loai' => 'phat_am',
                'thu_tu' => 8,
                'noi_dung_cau' => 'Phát âm đúng nguyên âm đơn (mặt chữ: ơ).',
                'diem_toi_da' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('cau_hoi_kiem_tras')->insert($rows);
    }
}
