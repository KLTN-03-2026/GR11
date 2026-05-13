<?php

namespace Database\Seeders;

use App\Models\PhienKiemTra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Phiên đã nộp của học viên id 4: đủ chi tiết cho mọi câu, điểm khớp lựa chọn đúng / chấm mẫu phát âm; cập nhật {@see TienDoBaiHoc} bài 1.
 */
class PhienKiemTraMauSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = DB::table('bai_kiem_tras')->where('bai_hoc_id', 1)->first();
        if (! $quiz) {
            return;
        }

        $hocVienId = 4;

        DB::table('phien_kiem_tras')
            ->where('nguoi_dung_id', $hocVienId)
            ->where('bai_kiem_tra_id', $quiz->id)
            ->delete();

        $now = Carbon::now();
        $batDau = $now->copy()->subMinutes(12);
        $ketThuc = $now->copy()->subMinutes(2);

        $cauRows = DB::table('cau_hoi_kiem_tras')
            ->where('bai_kiem_tra_id', $quiz->id)
            ->orderBy('thu_tu')
            ->get()
            ->keyBy('thu_tu');

        if ($cauRows->count() < 8) {
            return;
        }

        foreach ([1, 2, 3, 4] as $tt) {
            $cau = $cauRows->get($tt);
            $ok = DB::table('lua_chon_cau_hois')
                ->where('cau_hoi_kiem_tra_id', $cau->id)
                ->where('la_dung', true)
                ->exists();
            if (! $ok) {
                return;
            }
        }

        $phanHoiMau = json_encode([
            'van_ban_nhan_dien' => '',
            'demo_seeder' => true,
            'ghi_chu' => 'Điểm mẫu cho hội đồng; không có file ghi âm thật.',
        ], JSON_UNESCAPED_UNICODE);

        DB::transaction(function () use ($quiz, $hocVienId, $batDau, $ketThuc, $now, $cauRows, $phanHoiMau): void {
            $phienId = (int) DB::table('phien_kiem_tras')->insertGetId([
                'nguoi_dung_id' => $hocVienId,
                'bai_kiem_tra_id' => $quiz->id,
                'thoi_gian_bat_dau' => $batDau,
                'thoi_gian_ket_thuc' => $ketThuc,
                'tong_diem' => 0,
                'trang_thai' => PhienKiemTra::TRANG_THAI_NOP,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $tong = 0;

            foreach ([1, 2, 3, 4] as $tt) {
                $cau = $cauRows->get($tt);
                $lua = DB::table('lua_chon_cau_hois')
                    ->where('cau_hoi_kiem_tra_id', $cau->id)
                    ->where('la_dung', true)
                    ->first();
                $diem = (int) $cau->diem_toi_da;
                $tong += $diem;
                DB::table('chi_tiet_phien_kiem_tras')->insert([
                    'phien_kiem_tra_id' => $phienId,
                    'cau_hoi_kiem_tra_id' => $cau->id,
                    'lua_chon_id' => $lua->id,
                    'file_ghi_am_url' => null,
                    'diem_dat' => $diem,
                    'phan_hoi' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ([5, 6, 7, 8] as $tt) {
                $cau = $cauRows->get($tt);
                $diem = (int) round((int) $cau->diem_toi_da * 0.85);
                $tong += $diem;
                DB::table('chi_tiet_phien_kiem_tras')->insert([
                    'phien_kiem_tra_id' => $phienId,
                    'cau_hoi_kiem_tra_id' => $cau->id,
                    'lua_chon_id' => null,
                    'file_ghi_am_url' => null,
                    'diem_dat' => $diem,
                    'phan_hoi' => $phanHoiMau,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('phien_kiem_tras')->where('id', $phienId)->update([
                'tong_diem' => $tong,
                'updated_at' => $now,
            ]);

            $diemToiThieu = (int) $quiz->diem_toi_thieu;
            $qua = $tong >= $diemToiThieu ? 1 : 0;
            $td = DB::table('tien_do_bai_hocs')
                ->where('hoc_vien_id', $hocVienId)
                ->where('bai_hoc_id', 1)
                ->first();

            if ($td) {
                $best = max((float) ($td->diem_kiem_tra ?? 0), (float) $tong);
                DB::table('tien_do_bai_hocs')->where('id', $td->id)->update([
                    'diem_kiem_tra' => round($best, 2),
                    'qua_kiem_tra' => ((int) ($td->qua_kiem_tra ?? 0) === 1 || $qua === 1) ? 1 : 0,
                    'thoi_gian_kiem_tra_cuoi' => $ketThuc,
                ]);
            } else {
                DB::table('tien_do_bai_hocs')->insert([
                    'hoc_vien_id' => $hocVienId,
                    'bai_hoc_id' => 1,
                    'so_tu_da_hoc' => 11,
                    'phan_tram_hoan_thanh' => 100,
                    'trang_thai' => 1,
                    'diem_trung_binh' => 88,
                    'thoi_gian_hoc_cuoi' => $ketThuc,
                    'diem_kiem_tra' => $tong,
                    'qua_kiem_tra' => $qua,
                    'thoi_gian_kiem_tra_cuoi' => $ketThuc,
                ]);
            }
        });
    }
}
