<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Thứ tự phụ thuộc khóa ngoại: vai trò → người dùng → bài/từ → quan hệ → phiên luyện → lỗi/bookmark.
     */
    public function run(): void
    {
        $this->call([
            VaiTroSeeder::class,
            QuyenSeeder::class,
            DanhMucSeeder::class,
            VaiTroQuyenSeeder::class,
            NguoiDungSeeder::class,
            BaiHocSeeder::class,
            ThongTinHocVienSeeder::class,
            TuVungSeeder::class,
            BaiKiemTraSeeder::class,
            CauHoiKiemTraSeeder::class,
            LuaChonCauHoiKiemTraSeeder::class,
            PhienKiemTraMauSeeder::class,
            QuanHeGvHvSeeder::class,
            GoiYLuyenTapSeeder::class,
            LoTrinhCaNhanSeeder::class,
            ChiTietLoTrinhSeeder::class,
            BasicLoTrinhForExistingUsersSeeder::class,
            PhienLuyenTapSeeder::class,
            ChiTietLuyenTapSeeder::class,
            DiemDanhLoiSeeder::class,
            TienDoHocTapSeeder::class,
            LichSuLoiPhatAmSeeder::class,
            CauHinhHeThongSeeder::class,
            BannerSeeder::class,
            ViSeeder::class,
            GoiPremiumSeeder::class,
            LoTrinhTraPhiSeeder::class,
        ]);
    }
}
