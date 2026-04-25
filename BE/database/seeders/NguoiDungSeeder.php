<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NguoiDungSeeder extends Seeder
{
    /** Mật khẩu demo thống nhất cho môi trường phát triển */
    public function run(): void
    {
        $now = Carbon::now();

        $nguoiDungs = [
            // Admin
            [
                'id' => 1,
                'ho_ten' => 'Nguyễn Văn Quản Trị',
                'email' => 'admin@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000001',
                'vai_tro_id' => 1, // Admin
                'ngay_sinh' => '1990-01-01',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Giáo viên 1
            [
                'id' => 2,
                'ho_ten' => 'Trần Thị Giáo Viên',
                'email' => 'teacher1@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000002',
                'vai_tro_id' => 2, // Giáo viên
                'ngay_sinh' => '1985-05-12',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Giáo viên 2
            [
                'id' => 3,
                'ho_ten' => 'Lê Văn Dạy',
                'email' => 'teacher2@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000003',
                'vai_tro_id' => 2, // Giáo viên
                'ngay_sinh' => '1988-09-20',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 1 (đã kích hoạt)
            [
                'id' => 4,
                'ho_ten' => 'Phạm Thị Học',
                'email' => 'student1@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000004',
                'vai_tro_id' => 3, // Sinh viên
                'ngay_sinh' => '2000-03-15',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 2 (chưa kích hoạt)
            [
                'id' => 5,
                'ho_ten' => 'Ngô Văn Học',
                'email' => 'student2@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000005',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-07-22',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 0, // chưa active
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => Str::random(40), // dùng để kích hoạt
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 3 (bị block)
            [
                'id' => 6,
                'ho_ten' => 'Vũ Thị Khóa',
                'email' => 'student3@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000006',
                'vai_tro_id' => 3,
                'ngay_sinh' => '1999-11-02',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 2, // block
                'type_account' => 0,
                'content_block' => 'Vi phạm nội quy',
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 4
            [
                'id' => 7,
                'ho_ten' => 'Đặng Minh Học',
                'email' => 'student4@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000007',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-02-10',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 5
            [
                'id' => 8,
                'ho_ten' => 'Hoàng Thị Thảo',
                'email' => 'student5@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000008',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2000-12-01',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 6
            [
                'id' => 9,
                'ho_ten' => 'Bùi Văn Tài',
                'email' => 'student6@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000009',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-06-30',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 7 (yêu cầu reset mật khẩu)
            [
                'id' => 10,
                'ho_ten' => 'Trịnh Thị Yêu',
                'email' => 'student7@example.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000010',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-08-08',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 1,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => Str::random(40), // token reset mật khẩu
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('nguoi_dungs')->upsert(
            $nguoiDungs,
            ['id'],
            [
                'ho_ten',
                'email',
                'mat_khau',
                'sdt',
                'vai_tro_id',
                'ngay_sinh',
                'anh_dai_dien',
                'ngay_tao',
                'trang_thai',
                'type_account',
                'content_block',
                'hash_active',
                'hash_reset',
                'updated_at'
            ]
        );
    }
}
