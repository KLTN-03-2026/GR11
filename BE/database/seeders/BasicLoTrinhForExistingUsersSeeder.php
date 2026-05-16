<?php

namespace Database\Seeders;

use App\Models\ChiTietLoTrinh;
use App\Models\LoTrinhCaNhan;
use App\Models\NguoiDung;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BasicLoTrinhForExistingUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baiHocIds = [1,2,3,4,5];

        NguoiDung::chunk(100, function($users) use ($baiHocIds) {
            foreach ($users as $user) {
                if (!isset($user->vai_tro_id) || intval($user->vai_tro_id) !== 3) {
                    continue;
                }

                // Lấy danh sách bài học thực sự tồn tại
                $existingBaiHocIds = DB::table('bai_hocs')
                    ->whereIn('id', $baiHocIds)
                    ->pluck('id')
                    ->toArray();

                if (empty($existingBaiHocIds)) {
                    Log::warning("No basic lessons found for seeder; skipping user_id {$user->id}");
                    continue;
                }

                $defaultTeacher = NguoiDung::where('vai_tro_id', 2)->first();
                $giaoVienId = $defaultTeacher ? $defaultTeacher->id : 2;

                DB::beginTransaction();
                try {
                    $loTrinh = LoTrinhCaNhan::firstOrCreate(
                        ['hoc_vien_id' => $user->id, 'ten_lo_trinh' => 'Lộ trình cơ bản'],
                        ['giao_vien_id' => $giaoVienId]
                    );

                    foreach ($existingBaiHocIds as $index => $baiHocId) {
                        $existsDetail = ChiTietLoTrinh::where('lo_trinh_id', $loTrinh->id)
                            ->where('bai_hoc_id', $baiHocId)
                            ->exists();

                        if ($existsDetail) {
                            continue;
                        }

                        ChiTietLoTrinh::create([
                            'lo_trinh_id' => $loTrinh->id,
                            'bai_hoc_id' => $baiHocId,
                            'thu_tu_uu_tien' => $index + 1,
                            'ghi_chu_gv' => null,
                        ]);
                    }

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    Log::error("BasicLoTrinhForExistingUsersSeeder failed for user_id {$user->id}: {$e->getMessage()}", ['exception' => $e]);
                }
            }
        });
    }
}
