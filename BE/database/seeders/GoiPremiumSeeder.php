<?php

namespace Database\Seeders;

use App\GoiPremium\GoiPremiumDoiTuong;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoiPremiumSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $packages = [
            [
                'doi_tuong' => GoiPremiumDoiTuong::HocVien->value,
                'ten_goi' => 'Premium Học viên',
                'mo_ta' => 'Gợi ý AI chi tiết sau mỗi lượt ghi âm, báo cáo tiến độ theo tuần và ưu tiên hàng đợi chấm phát âm.',
                'gia' => 149000,
                'thoi_han_ngay' => 30,
                'tinh_nang' => json_encode([
                    'uu_tien_cham_diem' => true,
                    'bao_cao_tuan' => true,
                    'bao_cao_thang' => true,
                    'bieu_do_phat_am' => true,
                ], JSON_UNESCAPED_UNICODE),
                'trang_thai' => 1,
            ],
            [
                'doi_tuong' => GoiPremiumDoiTuong::GiaoVien->value,
                'ten_goi' => 'Premium Giáo viên',
                'mo_ta' => 'Mở rộng lớp học, thống kê lỗi phát âm theo học viên và gói gợi ý soạn bài luyện tập nhanh.',
                'gia' => 199000,
                'thoi_han_ngay' => 30,
                'tinh_nang' => json_encode(['gioi_han_hoc_vien' => 80, 'bao_cao_lop' => true], JSON_UNESCAPED_UNICODE),
                'trang_thai' => 1,
            ],
        ];

        foreach ($packages as $p) {
            $exists = DB::table('goi_premiums')->where('doi_tuong', $p['doi_tuong'])->exists();
            if ($exists) {
                DB::table('goi_premiums')->where('doi_tuong', $p['doi_tuong'])->update([
                    'ten_goi' => $p['ten_goi'],
                    'mo_ta' => $p['mo_ta'],
                    'gia' => $p['gia'],
                    'thoi_han_ngay' => $p['thoi_han_ngay'],
                    'tinh_nang' => $p['tinh_nang'],
                    'trang_thai' => $p['trang_thai'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('goi_premiums')->insert(array_merge($p, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
