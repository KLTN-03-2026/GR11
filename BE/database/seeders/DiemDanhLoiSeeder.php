<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiemDanhLoiSeeder extends Seeder
{
    private const HOC_VIEN_EMAILS = [
        'giabao.student@gmail.com',
        'minhan.hv@gmail.com',
        'thaovy.kid@outlook.com',
        'hoanglong.phuong@gmail.com',
        'khanhlinh.hv@gmail.com',
    ];

    private const MUC_DO = ['thap', 'binh_thuong', 'cao'];

    public function run(): void
    {
        $hocVienIds = DB::table('nguoi_dungs')->whereIn('email', self::HOC_VIEN_EMAILS)->pluck('id');
        if ($hocVienIds->isEmpty()) {
            return;
        }

        DB::table('diem_danh_lois')->whereIn('nguoi_dung_id', $hocVienIds)->delete();

        $tuVungs = DB::table('tu_vungs')->get(['id', 'bai_hoc_id']);
        if ($tuVungs->isEmpty()) {
            return;
        }

        $ghiChuMau = [
            'Ôn lại vào cuối tuần cùng mẹ.',
            'Nghe file mẫu 3 lần trước khi ghi âm.',
            'Bé thích từ này — thưởng sticker nếu đạt ≥90 điểm.',
            'Giáo viên dặn luyện từng âm đầu chậm.',
            'Ghép với tranh minh họa trong sổ tay.',
        ];

        mt_srand(7);

        foreach ($hocVienIds as $hvId) {
            $bookmarkTu = $tuVungs->shuffle()->take(random_int(4, 9));

            foreach ($bookmarkTu as $tu) {
                $hoanThanh = (bool) random_int(0, 1);
                $ngayDanhDau = Carbon::now()->subDays(random_int(2, 25));

                DB::table('diem_danh_lois')->insert([
                    'nguoi_dung_id' => $hvId,
                    'tu_vung_id' => $tu->id,
                    'bai_hoc_id' => $tu->bai_hoc_id,
                    'muc_do_uu_tien' => self::MUC_DO[array_rand(self::MUC_DO)],
                    'ghi_chu' => $ghiChuMau[array_rand($ghiChuMau)],
                    'da_hoan_thanh' => $hoanThanh,
                    'ngay_danh_dau' => $ngayDanhDau,
                    'ngay_hoan_thanh' => $hoanThanh ? (clone $ngayDanhDau)->addDays(random_int(0, 5)) : null,
                    'ngay_tao' => $ngayDanhDau,
                    'ngay_cap_nhat' => now(),
                ]);
            }
        }
    }
}
