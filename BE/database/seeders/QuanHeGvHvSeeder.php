<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuanHeGvHvSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $quanHeGvHvs = [
            ['giao_vien_id' => 2, 'hoc_vien_id' => 4, 'trang_thai' => 1, 'ngay_ket_noi' => $now],
            ['giao_vien_id' => 2, 'hoc_vien_id' => 5, 'trang_thai' => 1, 'ngay_ket_noi' => $now->copy()->subDays(2)],
            ['giao_vien_id' => 3, 'hoc_vien_id' => 7, 'trang_thai' => 1, 'ngay_ket_noi' => $now->copy()->subDays(5)],
            ['giao_vien_id' => 3, 'hoc_vien_id' => 8, 'trang_thai' => 1, 'ngay_ket_noi' => $now->copy()->subDays(10)],
            ['giao_vien_id' => 2, 'hoc_vien_id' => 9, 'trang_thai' => 1, 'ngay_ket_noi' => $now->copy()->subDays(1)],
            ['giao_vien_id' => 2, 'hoc_vien_id' => 10, 'trang_thai' => 1, 'ngay_ket_noi' => $now->copy()->subDays(3)],
            ['giao_vien_id' => 3, 'hoc_vien_id' => 4, 'trang_thai' => 1, 'ngay_ket_noi' => $now->copy()->subDays(7)],
        ];

        foreach ($quanHeGvHvs as $i => $row) {
            $quanHeGvHvs[$i]['created_at'] = $row['ngay_ket_noi'];
            $quanHeGvHvs[$i]['updated_at'] = $now;
        }

        DB::table('quan_he_gv_hvs')->upsert(
            $quanHeGvHvs,
            ['giao_vien_id', 'hoc_vien_id'],
            ['trang_thai', 'ngay_ket_noi', 'updated_at']
        );
    }
}
