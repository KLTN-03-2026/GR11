<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TienDoHocTapSeeder extends Seeder
{
    private const HOC_VIEN_EMAILS = [
        'giabao.student@gmail.com',
        'minhan.hv@gmail.com',
        'thaovy.kid@outlook.com',
        'hoanglong.phuong@gmail.com',
        'khanhlinh.hv@gmail.com',
    ];

    private const TRANG_THAI = ['da_dat', 'cho_duyet', 'can_luyen_them'];

    /**
     * Tiến độ từng từ: học viên khác nhau — điểm và số lần luyện phân tán thực tế.
     */
    public function run(): void
    {
        $hocVienIds = DB::table('nguoi_dungs')->whereIn('email', self::HOC_VIEN_EMAILS)->pluck('id');
        $tuVungs = DB::table('tu_vungs')->pluck('id');

        if ($hocVienIds->isEmpty() || $tuVungs->isEmpty()) {
            return;
        }

        mt_srand(33);

        foreach ($hocVienIds as $hvId) {
            foreach ($tuVungs as $tuId) {
                $score = random_int(55, 100);
                DB::table('tien_do_hoc_taps')->updateOrInsert(
                    ['nguoi_dung_id' => $hvId, 'tu_vung_id' => $tuId],
                    [
                        'trang_thai' => $score >= 85 ? 'da_dat' : self::TRANG_THAI[array_rand(self::TRANG_THAI)],
                        'so_lan_luyen_tap' => random_int(1, 18),
                        'diem_cao_nhat' => $score,
                        'ngay_cap_nhat_cuoi' => now()->subDays(random_int(0, 20)),
                    ]
                );
            }
        }
    }
}
