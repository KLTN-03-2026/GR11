<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach (DB::table('nguoi_dungs')->orderBy('id')->get(['id', 'vai_tro_id']) as $u) {
            $soDu = match ((int) $u->vai_tro_id) {
                1 => 0,
                2 => 250_000,
                3 => 50_000,
                default => 0,
            };

            if (DB::table('vis')->where('nguoi_dung_id', $u->id)->exists()) {
                DB::table('vis')->where('nguoi_dung_id', $u->id)->update([
                    'so_du' => $soDu,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('vis')->insert([
                    'nguoi_dung_id' => $u->id,
                    'so_du' => $soDu,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
