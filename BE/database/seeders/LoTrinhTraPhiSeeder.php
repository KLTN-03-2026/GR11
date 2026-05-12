<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoTrinhTraPhiSeeder extends Seeder
{
    public function run(): void
    {
        $loTrinhs = DB::table('lo_trinh_ca_nhans')->limit(3)->get();

        if ($loTrinhs->isEmpty()) {
            $this->command->warn('Không có lộ trình nào trong DB. Bỏ qua LoTrinhTraPhiSeeder.');
            return;
        }

        $now = now();
        $data = $loTrinhs->map(fn ($lt) => [
            'lo_trinh_id' => $lt->id,
            'gia' => 149000,
            'mo_ta_ban' => 'Lộ trình phát âm nâng cao do giáo viên biên soạn.',
            'trang_thai' => 1,
            'ngay_duyet' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        DB::table('lo_trinh_tra_phis')->insertOrIgnore($data);
    }
}
