<?php

namespace Database\Seeders;

use App\Models\BaiHoc;
use App\Models\TuVung;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
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
            QuanHeGvHvSeeder::class,
            LoTrinhCaNhanSeeder::class,
            ChiTieLoTrinhSeeder::class,
            PhienLuyenTapSeeder::class,
            ChiTietLuyenTapSeeder::class,
            TienDoHocTapSeeder::class,
            CauHinhHeThongSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
