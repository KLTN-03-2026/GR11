<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoTrinhCaNhanSeeder extends Seeder
{
    /**
     * Khởi tạo lộ trình cá nhân hóa cho từng học viên.
     */
    public function run(): void
    {
        $gv1 = DB::table('nguoi_dungs')->where('email', 'maiphuong.gv@gmail.com')->first();
        $gv2 = DB::table('nguoi_dungs')->where('email', 'thuylinh.gv@gmail.com')->first();

        if (! $gv1) {
            return;
        }

        $cacLoTrinh = [
            [
                'hv_email' => 'giabao.student@gmail.com',
                'giao_vien_id' => $gv1->id,
                'ten' => 'Lộ trình: Khắc phục lỗi L/N và chuẩn hóa dấu thanh',
                'ngay' => Carbon::parse('2026-04-01'),
            ],
            [
                'hv_email' => 'minhan.hv@gmail.com',
                'giao_vien_id' => ($gv2 ?? $gv1)->id,
                'ten' => 'Luyện phát âm chuẩn phụ âm quặt lưỡi S/X và TR/CH',
                'ngay' => Carbon::parse('2026-03-15'),
            ],
            [
                'hv_email' => 'thaovy.kid@outlook.com',
                'giao_vien_id' => $gv1->id,
                'ten' => 'Giao tiếp tự tin: Từ vựng đời sống và câu đơn lịch sự',
                'ngay' => Carbon::parse('2026-04-05'),
            ],
            [
                'hv_email' => 'hoanglong.phuong@gmail.com',
                'giao_vien_id' => ($gv2 ?? $gv1)->id,
                'ten' => 'Khởi động hơi và luyện phát âm nguyên âm mở miệng',
                'ngay' => Carbon::parse('2026-04-10'),
            ],
        ];

        foreach ($cacLoTrinh as $lt) {
            $hv = DB::table('nguoi_dungs')->where('email', $lt['hv_email'])->first();

            if (! $hv) {
                continue;
            }

            DB::table('lo_trinh_ca_nhans')->updateOrInsert(
                [
                    'giao_vien_id' => $lt['giao_vien_id'],
                    'hoc_vien_id' => $hv->id,
                    'ten_lo_trinh' => $lt['ten'],
                ],
                [
                    'ngay_tao' => $lt['ngay']->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
