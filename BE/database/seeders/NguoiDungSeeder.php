<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    /** Mật khẩu demo thống nhất cho môi trường phát triển */
    private const MAT_KHAU_DEMO = '123456';

    /**
     * Tài khoản mẫu: admin, giáo viên, học viên — email unique, SĐT đúng 10 số.
     */
    public function run(): void
    {
        $adminRole = DB::table('vai_tros')->where('ten_vai_tro', 'admin')->first()->id;
        $gvRole = DB::table('vai_tros')->where('ten_vai_tro', 'giao_vien')->first()->id;
        $hvRole = DB::table('vai_tros')->where('ten_vai_tro', 'nguoi_dung')->first()->id;

        $hash = Hash::make(self::MAT_KHAU_DEMO);

        $users = [
            // Quản trị
            [
                'ho_ten' => 'Quản trị viên EchoKids',
                'email' => 'admin@echokids.edu.vn',
                'mat_khau' => $hash,
                'sdt' => '0901122334',
                'vai_tro_id' => $adminRole,
                'trang_thai' => 0,
            ],
            // Giáo viên
            [
                'ho_ten' => 'Cô Nguyễn Mai Phương',
                'email' => 'maiphuong.gv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0987654321',
                'vai_tro_id' => $gvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Cô Trần Thùy Linh',
                'email' => 'thuylinh.gv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0911223344',
                'vai_tro_id' => $gvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Thầy Trần Phong',
                'email' => 'tranphong.gv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0922334455',
                'vai_tro_id' => $gvRole,
                'trang_thai' => 0,
            ],
            // Học viên (phụ huynh đăng ký)
            [
                'ho_ten' => 'Bé Gia Bảo Nguyễn',
                'email' => 'giabao.student@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0912345678',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Minh An Lê',
                'email' => 'minhan.hv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0923456789',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Thảo Vy Phạm',
                'email' => 'thaovy.kid@outlook.com',
                'mat_khau' => $hash,
                'sdt' => '0934567890',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Hoàng Long Đặng',
                'email' => 'hoanglong.phuong@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0945678901',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Khánh Linh Võ',
                'email' => 'khanhlinh.hv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0956789012',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Hà Anh Trần',
                'email' => 'haanh.hv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0967890123',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Tùng Lâm Nguyễn',
                'email' => 'tunglam.kid@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0978901234',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
            [
                'ho_ten' => 'Bé Hiền Trang Hoàng',
                'email' => 'hientrang.hv@gmail.com',
                'mat_khau' => $hash,
                'sdt' => '0989012345',
                'vai_tro_id' => $hvRole,
                'trang_thai' => 0,
            ],
        ];

        foreach ($users as $user) {
            DB::table('nguoi_dungs')->updateOrInsert(['email' => $user['email']], $user);
        }
    }
}