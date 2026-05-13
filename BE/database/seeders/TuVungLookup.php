<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

/**
 * Tra id từ vựng theo bài học + mặt chữ (đúng từ điển {@see TuVungSeeder}), không hard-code id tuyến tính.
 */
final class TuVungLookup
{
    public static function id(int $baiHocId, string $tuChuan): ?int
    {
        return DB::table('tu_vungs')
            ->where('bai_hoc_id', $baiHocId)
            ->where('tu_chuan', $tuChuan)
            ->value('id');
    }

    /** @return list<int> */
    public static function firstIdsForLesson(int $baiHocId, int $n): array
    {
        return DB::table('tu_vungs')
            ->where('bai_hoc_id', $baiHocId)
            ->orderBy('id')
            ->limit($n)
            ->pluck('id')
            ->all();
    }
}
