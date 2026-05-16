<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Models\ChiTietLoTrinh;
use App\Models\LoTrinhCaNhan;
use Illuminate\Auth\Events\Registered as EventsRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateBasicLoTrinh implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EventsRegistered $event): void
    {
        $user = $event->user;

        if (!isset($user->vai_tro_id) || intval($user->vai_tro_id) !== 3) {
            return;
        }

        $exists = LoTrinhCaNhan::where('hoc_vien_id', $user->id)
            ->where('ten_lo_trinh', 'Lộ trình cơ bản')
            ->exists();

        if ($exists) {
            return;
        }

        $baiHocIds = [1,2,3,4,5];

        DB::beginTransaction();
        try {
            $giaoVienId = 2;

            $loTrinh = LoTrinhCaNhan::create([
                'hoc_vien_id' => $user->id,
                'giao_vien_id' => $giaoVienId,
                'ten_lo_trinh' => 'Lộ trình cơ bản',
            ]);

            $existingBaiHocIds = DB::table('bai_hocs')
                ->whereIn('id', $baiHocIds)
                ->pluck('id')
                ->toArray();

            foreach ($existingBaiHocIds as $index => $baiHocId) {
                ChiTietLoTrinh::create([
                    'lo_trinh_id' => $loTrinh->id,
                    'bai_hoc_id' => $baiHocId,
                    'thu_tu_uu_tien' => $index + 1,
                    'ghi_chu_gv' => null,
                ]);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('CreateBasicLoTrinh failed for user_id '.$user->id.': '.$e->getMessage());
        }
    }
}
