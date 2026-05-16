<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Một đề kiểm tra xuất bản cho bài học 1 (Nguyên âm đơn), gắn với từ vựng id 1–11 trong {@see TuVungSeeder}.
 */
class BaiKiemTraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bai_kiem_tras')->where('bai_hoc_id', 1)->delete();

        $now = Carbon::now();

        DB::table('bai_kiem_tras')->insert([
            'bai_hoc_id' => 1,
            'nguoi_tao_id' => 2,
            'tieu_de' => 'Kiểm tra: 11 nguyên âm đơn',
            'mo_ta_huong_dan' => 'Phần trắc nghiệm kiểm tra nhận biết mặt chữ và phiên âm mẫu đúng theo từ vựng bài học. Phần phát âm: đọc đúng các nguyên âm đơn đã học.',
            'thoi_gian_gioi_han_giay' => 900,
            'diem_toi_thieu' => 45,
            'trang_thai' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
