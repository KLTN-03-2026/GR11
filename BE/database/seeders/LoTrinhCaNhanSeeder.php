<?php

namespace Database\Seeders;

use App\Models\LoTrinhCaNhan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoTrinhCaNhanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        foreach (range(4, 10) as $hocVienId) {
            LoTrinhCaNhan::updateOrCreate(
                ['hoc_vien_id' => $hocVienId, 'ten_lo_trinh' => 'Lộ trình cơ bản'],
                ['giao_vien_id' => 2, 'updated_at' => $now]
            );
        }

        LoTrinhCaNhan::updateOrCreate(
            ['hoc_vien_id' => 4, 'ten_lo_trinh' => 'Lộ trình: nguyên âm & phụ âm môi'],
            ['giao_vien_id' => 2, 'updated_at' => $now]
        );

        LoTrinhCaNhan::updateOrCreate(
            ['hoc_vien_id' => 5, 'ten_lo_trinh' => 'Lộ trình: phụ âm quặt lưỡi (tr, ch, r)'],
            ['giao_vien_id' => 3, 'updated_at' => $now]
        );
    }
}
