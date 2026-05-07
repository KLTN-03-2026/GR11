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
                'ho_ten' => 'Quản Trị Viên Echokids',
                'email' => 'admin@master.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000001',
                'vai_tro_id' => 1, // Admin
                'ngay_sinh' => '1990-01-01',
                'anh_dai_dien' => null,
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'anh_dai_dien' => "https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:format(webp):quality(75)/avatar_vo_tri_a49436c5de.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'anh_dai_dien' => "https://hanoidep.vn/wp-content/uploads/2025/11/0757ff5479a506b80513d83673790e53.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'ho_ten' => 'Trần Thị Thu Hà',
                'email' => 'tranthithuha@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000002',
                'vai_tro_id' => 2, // Giáo viên
                'ngay_sinh' => '1985-05-12',
                'anh_dai_dien' => "https://image.dienthoaivui.com.vn/x,webp,q90/https://dashboard.dienthoaivui.com.vn/uploads/dashboard/editor_upload/avatar-cute-34.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'ho_ten' => 'Lê Minh Tuấn',
                'email' => 'leminhtuan@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000003',
                'vai_tro_id' => 2, // Giáo viên
                'ngay_sinh' => '1988-09-20',
                'anh_dai_dien' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSSfz5LOeGinM5ZRqGmhBaFLtk81drA9C1dbQ&s",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'ho_ten' => 'Phạm Lan Anh',
                'email' => 'phamlananh@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000004',
                'vai_tro_id' => 3, // Sinh viên
                'ngay_sinh' => '2000-03-15',
                'anh_dai_dien' => "https://thuvienavatar.edu.vn/wp-content/uploads/2025/12/avatar-4-nguoi-ban-than-1.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'ho_ten' => 'Ngô Đức Trí',
                'email' => 'ngoductri@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000005',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-07-22',
                'anh_dai_dien' => "https://avatarngau.sbs/wp-content/uploads/2025/05/avatar-meme-3.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 1, // tạm khóa/chưa active
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
                'ho_ten' => 'Vũ Ngọc Mai',
                'email' => 'vungocmai@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000006',
                'vai_tro_id' => 3,
                'ngay_sinh' => '1999-11-02',
                'anh_dai_dien' => "https://s3.ap-southeast-1.amazonaws.com/cdn.vntre.vn/default/avatar-anime-17-1724512404.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 1, // tạm khóa
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
                'ho_ten' => 'Đặng Minh Khôi',
                'email' => 'dangminhkhoi@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000007',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-02-10',
                'anh_dai_dien' => "https://cellphones.com.vn/sforum/wp-content/uploads/2024/02/anh-avatar-cute-70.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'email' => 'hoangthithao@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000008',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2000-12-01',
                'anh_dai_dien' => "https://bom.edu.vn/public/upload/2024/12/avatar-cute-khung-long-1.webp",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'email' => 'buivantai@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000009',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-06-30',
                'anh_dai_dien' => "https://capthathinh.com/wp-content/uploads/2026/03/Hinh-avatar-dep-nhat-the-gioi-14.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
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
                'ho_ten' => 'Trịnh Kiều Yến',
                'email' => 'trinhkieuyen@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000010',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-08-08',
                'anh_dai_dien' => "https://i.pinimg.com/736x/d0/eb/c7/d0ebc736a1a914c333814ab7a64182c0.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => Str::random(40), // token reset mật khẩu
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Sinh viên 8
            [
                'id' => 26,
                'ho_ten' => 'Nguyễn Thu Phương',
                'email' => 'nguyenthuphuong@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000026',
                'vai_tro_id' => 3, // Sinh viên
                'ngay_sinh' => '2003-01-15',
                'anh_dai_dien' => "https://antimatter.vn/wp-content/uploads/2022/05/hinh-anh-avatar-tet-600x600.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 9
            [
                'id' => 27,
                'ho_ten' => 'Trần Văn Quyết',
                'email' => 'tranvanquyet@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000027',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-05-22',
                'anh_dai_dien' => "https://i.9mobi.vn/cf/Images/tt/2021/8/20/anh-avatar-dep-55.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 10
            [
                'id' => 28,
                'ho_ten' => 'Lê Thanh Huyền',
                'email' => 'lethanhhuyen@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000028',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-09-08',
                'anh_dai_dien' => "https://thuvienavatar.edu.vn/wp-content/uploads/2025/11/avatar-con-trai-cute-16.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 11
            [
                'id' => 29,
                'ho_ten' => 'Phạm Quốc Bảo',
                'email' => 'phamquocbao@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000029',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2004-11-30',
                'anh_dai_dien' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTyH4csshD9BR3NSgz8Iomz3maR2L5O8XHcOQ&s",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 12 (chưa kích hoạt)
            [
                'id' => 30,
                'ho_ten' => 'Hoàng Minh Ngọc',
                'email' => 'hoangminhngoc@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000030',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2003-02-14',
                'anh_dai_dien' => "https://chiemtaimobile.vn/images/companies/1/%E1%BA%A2nh%20Blog/avatar-facebook-dep/Bo-suu-tap-anh-avatar-anime-nu-co-gai-bang-do-do.jpg?1704789148675",
                'ngay_tao' => $now,
                'trang_thai' => 1, // Tạm khóa / chưa active
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => Str::random(40),
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 13
            [
                'id' => 31,
                'ho_ten' => 'Vũ Thị Thanh',
                'email' => 'vuthithanh@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000031',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2000-08-19',
                'anh_dai_dien' => "https://hhfandom.com/wp-content/uploads/2025/11/Bo-hinh-avatar-ca-map-cute.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 14 (bị block)
            [
                'id' => 32,
                'ho_ten' => 'Ngô Xuân Dũng',
                'email' => 'ngoxuandung@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000032',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-04-05',
                'anh_dai_dien' => "https://heucollege.edu.vn/upload/2025/02/hinh-avatar-hoat-hinh-001.webp",
                'ngay_tao' => $now,
                'trang_thai' => 1, // Bị khóa
                'type_account' => 0,
                'content_block' => 'Sử dụng ngôn từ không phù hợp trên diễn đàn',
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 15
            [
                'id' => 33,
                'ho_ten' => 'Đặng Kim Ngân',
                'email' => 'dangkimngan@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000033',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-12-25',
                'anh_dai_dien' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ6NHeYiK2MspAl2z4mOaAHtMrkSwR16a1h0w&s",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 16
            [
                'id' => 34,
                'ho_ten' => 'Bùi Trọng Nghĩa',
                'email' => 'buitrongnghia@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000034',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2003-07-07',
                'anh_dai_dien' => "https://vn1.vdrive.vn/alohamedia.vn/2025/08/fe4832ae2a3e37f030d13e5e2c131196.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 17
            [
                'id' => 35,
                'ho_ten' => 'Trịnh Hoàng Long',
                'email' => 'trinhhoanglong@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000035',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-03-12',
                'anh_dai_dien' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTsSKvjO3-9aph5IkzbsxJsHmETRV0EL6BZjg&s",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 18
            [
                'id' => 36,
                'ho_ten' => 'Lý Kiều Loan',
                'email' => 'lykieuloan@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000036',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2004-06-18',
                'anh_dai_dien' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyfN60jRkB_DEiXObbV2y2Q16PmrI7WlN6hQ&s",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 19 (yêu cầu reset mật khẩu)
            [
                'id' => 37,
                'ho_ten' => 'Đỗ Đình Toàn',
                'email' => 'dodinhtoan@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000037',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-10-09',
                'anh_dai_dien' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS_riIo9MRbqxIARLKXBddaGiZfExypNYaJsQ&s",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => Str::random(40),
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 20
            [
                'id' => 38,
                'ho_ten' => 'Hồ Gia Huy',
                'email' => 'hogiahuy@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000038',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2003-09-24',
                'anh_dai_dien' => "https://s3.ap-southeast-1.amazonaws.com/cdn.vntre.vn/default/avatar-anime-17-1724512404.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 21
            [
                'id' => 39,
                'ho_ten' => 'Dương Thúy Vi',
                'email' => 'duongthuyvi@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000039',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2001-11-17',
                'anh_dai_dien' => "https://antimatter.vn/wp-content/uploads/2023/02/hinh-anh-avatar-facebook-ngo-nghinh.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sinh viên 22
            [
                'id' => 40,
                'ho_ten' => 'Đinh Xuân Phúc',
                'email' => 'dinhxuanphuc@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'sdt' => '0900000040',
                'vai_tro_id' => 3,
                'ngay_sinh' => '2002-03-31',
                'anh_dai_dien' => "https://linhkien283.com/wp-content/uploads/2025/10/Hinh-anh-avatar-vo-tri-cute-1.jpg",
                'ngay_tao' => $now,
                'trang_thai' => 0,
                'type_account' => 0,
                'content_block' => null,
                'hash_active' => null,
                'hash_reset' => null,
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
                'updated_at',
            ]
        );
    }
}
