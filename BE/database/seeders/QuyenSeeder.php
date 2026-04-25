<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $quyens = [
            [
                'id' => 1,
                'ten_quyen' => 'Đăng ký tài khoản',
                'ma_quyen' => Str::slug('auth.register'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'ten_quyen' => 'Kích hoạt tài khoản',
                'ma_quyen' => Str::slug('auth.activate'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'ten_quyen' => 'Đăng nhập / Đăng xuất',
                'ma_quyen' => Str::slug('auth.login'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'ten_quyen' => 'Quên mật khẩu / Đặt lại mật khẩu',
                'ma_quyen' => Str::slug('auth.reset'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'ten_quyen' => 'Quản lý người dùng - tạo',
                'ma_quyen' => Str::slug('user.create'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'ten_quyen' => 'Quản lý người dùng - xem',
                'ma_quyen' => Str::slug('user.read'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'ten_quyen' => 'Quản lý người dùng - sửa',
                'ma_quyen' => Str::slug('user.update'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'ten_quyen' => 'Quản lý người dùng - xóa',
                'ma_quyen' => Str::slug('user.delete'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'ten_quyen' => 'CRUD vai trò',
                'ma_quyen' => Str::slug('role.crud'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'ten_quyen' => 'CRUD quyền',
                'ma_quyen' => Str::slug('permission.crud'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'ten_quyen' => 'Gán / thu hồi quyền cho vai trò',
                'ma_quyen' => Str::slug('role.assign'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'ten_quyen' => 'Tạo khóa học',
                'ma_quyen' => Str::slug('course.create'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'ten_quyen' => 'Xem khóa học',
                'ma_quyen' => Str::slug('course.read'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 14,
                'ten_quyen' => 'Sửa khóa học',
                'ma_quyen' => Str::slug('course.update'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'ten_quyen' => 'Xóa khóa học',
                'ma_quyen' => Str::slug('course.delete'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 16,
                'ten_quyen' => 'Quản lý từ vựng (thêm/sửa/xóa)',
                'ma_quyen' => Str::slug('vocab.manage'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'ten_quyen' => 'Quản lý lộ trình cá nhân',
                'ma_quyen' => Str::slug('roadmap.manage'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'ten_quyen' => 'Quản lý quan hệ giáo viên - học viên',
                'ma_quyen' => Str::slug('relation.gv_hv'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 19,
                'ten_quyen' => 'Tạo phiên luyện tập',
                'ma_quyen' => Str::slug('session.create'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 20,
                'ten_quyen' => 'Lưu chi tiết luyện tập / kết quả AI',
                'ma_quyen' => Str::slug('session.result.save'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 21,
                'ten_quyen' => 'Tải lên / quản lý file âm thanh',
                'ma_quyen' => Str::slug('file.audio.upload'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'ten_quyen' => 'Xem báo cáo tiến độ học viên',
                'ma_quyen' => Str::slug('report.progress.view'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 23,
                'ten_quyen' => 'Quản lý thông báo (tạo/gửi)',
                'ma_quyen' => Str::slug('notification.manage'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 24,
                'ten_quyen' => 'Quản lý cấu hình hệ thống',
                'ma_quyen' => Str::slug('config.manage'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 25,
                'ten_quyen' => 'Quản lý banner',
                'ma_quyen' => Str::slug('banner.manage'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 26,
                'ten_quyen' => 'Xem dashboard admin',
                'ma_quyen' => Str::slug('dashboard.view'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 27,
                'ten_quyen' => 'Audit log hành động quan trọng',
                'ma_quyen' => Str::slug('audit.log.view'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 28,
                'ten_quyen' => 'Export báo cáo (JSON/CSV)',
                'ma_quyen' => Str::slug('report.export'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('quyens')->upsert(
            $quyens,
            ['id'],
            ['ten_quyen', 'ma_quyen', 'updated_at']
        );
    }
}
