<?php

namespace Database\Seeders;

use App\Models\ChiTietLoTrinh;
use App\Models\LoTrinhCaNhan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ChiTietLoTrinhSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = Carbon::now();

        $basicRows = [
            ['bai_hoc_id' => 1, 'thu_tu_uu_tien' => 1, 'ghi_chu_gv' => 'Ôn 11 nguyên âm đơn; chuẩn bị cho bài kiểm tra tổng hợp.'],
            ['bai_hoc_id' => 2, 'thu_tu_uu_tien' => 2, 'ghi_chu_gv' => 'Phụ âm môi b, m, p, ph — tập bật hơi rõ.'],
            ['bai_hoc_id' => 3, 'thu_tu_uu_tien' => 3, 'ghi_chu_gv' => 'Phụ âm đầu lưỡi t, th, n, l, d.'],
        ];

        foreach (range(4, 10) as $hocVienId) {
            $lo = LoTrinhCaNhan::query()
                ->where('hoc_vien_id', $hocVienId)
                ->where('ten_lo_trinh', 'Lộ trình cơ bản')
                ->first();

            if (! $lo) {
                continue;
            }

            foreach ($basicRows as $row) {
                ChiTietLoTrinh::updateOrCreate(
                    [
                        'lo_trinh_id' => $lo->id,
                        'bai_hoc_id' => $row['bai_hoc_id'],
                    ],
                    [
                        'thu_tu_uu_tien' => $row['thu_tu_uu_tien'],
                        'ghi_chu_gv' => $row['ghi_chu_gv'],
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $lo4 = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', 4)
            ->where('ten_lo_trinh', 'Lộ trình: nguyên âm & phụ âm môi')
            ->first();

        if ($lo4) {
            foreach ([
                ['bai_hoc_id' => 1, 'thu_tu_uu_tien' => 1, 'ghi_chu_gv' => 'Nhận diện mặt chữ và phiên âm mẫu.'],
                ['bai_hoc_id' => 2, 'thu_tu_uu_tien' => 2, 'ghi_chu_gv' => 'Ghép âm môi với vần đơn giản.'],
            ] as $row) {
                ChiTietLoTrinh::updateOrCreate(
                    ['lo_trinh_id' => $lo4->id, 'bai_hoc_id' => $row['bai_hoc_id']],
                    [
                        'thu_tu_uu_tien' => $row['thu_tu_uu_tien'],
                        'ghi_chu_gv' => $row['ghi_chu_gv'],
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $lo5 = LoTrinhCaNhan::query()
            ->where('hoc_vien_id', 5)
            ->where('ten_lo_trinh', 'Lộ trình: phụ âm quặt lưỡi (tr, ch, r)')
            ->first();

        if ($lo5) {
            ChiTietLoTrinh::updateOrCreate(
                ['lo_trinh_id' => $lo5->id, 'bai_hoc_id' => 4],
                [
                    'thu_tu_uu_tien' => 1,
                    'ghi_chu_gv' => 'Tập trung tre / chó / rổ; tránh lẫn với âm cùng vị trí.',
                    'updated_at' => $now,
                ]
            );
        }
    }
}
