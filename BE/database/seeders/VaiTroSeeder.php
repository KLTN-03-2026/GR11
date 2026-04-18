<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaitros = [
            ['ten_vai_tro' => 'admin', 'mo_ta' => 'Quản trị viên toàn quyền hệ thống'],
            ['ten_vai_tro' => 'giao_vien', 'mo_ta' => 'Chuyên gia/Giáo viên quản lý học viên và lộ trình'],
            ['ten_vai_tro' => 'nguoi_dung', 'mo_ta' => 'Học viên hoặc Phụ huynh sử dụng hệ thống luyện tập'],
        ];
        foreach ($vaitros as $vt) {
            DB::table('vai_tros')->updateOrInsert(['ten_vai_tro' => $vt['ten_vai_tro']], $vt);
        }
    }
}
