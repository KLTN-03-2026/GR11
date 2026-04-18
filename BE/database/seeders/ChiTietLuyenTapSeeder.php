<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietLuyenTapSeeder extends Seeder
{
    /**
     * Chi tiết từng từ trong phiên — chỉ lấy từ vựng thuộc đúng bài học của phiên.
     */
    public function run(): void
    {
        $phiens = DB::table('phien_luyen_taps')->orderBy('id')->get();

        foreach ($phiens as $phien) {
            $soTuCoTrongBai = DB::table('tu_vungs')->where('bai_hoc_id', $phien->bai_hoc_id)->count();
            if ($soTuCoTrongBai === 0) {
                continue;
            }

            // Lấy ngẫu nhiên từ 4-8 từ trong bài để luyện tập
            $maxLay = max(1, min(random_int(4, 8), $soTuCoTrongBai));
            $tuTrongBai = DB::table('tu_vungs')
                ->where('bai_hoc_id', $phien->bai_hoc_id)
                ->inRandomOrder()
                ->limit($maxLay)
                ->get();

            foreach ($tuTrongBai as $tv) {
                // Tỷ lệ phát âm đúng là 70%
                $isCorrect = random_int(1, 10) > 3;

                // Xác định loại lỗi nếu phát âm sai
                $loiAmDau = ! $isCorrect && random_int(1, 3) === 1;
                $loiVan = ! $isCorrect && ! $loiAmDau && random_int(1, 2) === 1;
                $loiThanh = ! $isCorrect && ! $loiAmDau && ! $loiVan;

                // Giả lập văn bản AI nhận diện được
                $vanBanAi = $isCorrect
                    ? $tv->tu_chuan
                    : $this->giaLapLoiPhatAm($tv->tu_chuan, $loiAmDau, $loiVan, $loiThanh);

                DB::table('chi_tiet_luyen_taps')->insert([
                    'phien_id' => $phien->id,
                    'tu_vung_id' => $tv->id,
                    'file_ghi_am_url' => "audio/recordings/phien_{$phien->id}_tv_{$tv->id}.wav",
                    'van_ban_ai_nhan_dien' => $vanBanAi,
                    'diem_tin_cay' => round(random_int(72, 99) / 100, 2),
                    'diem_chinh_xac' => $isCorrect ? random_int(86, 100) : random_int(38, 74),
                    'loi_am_dau' => $loiAmDau,
                    'loi_van' => $loiVan,
                    'loi_thanh_dieu' => $loiThanh,
                    'chi_tiet_loi' => $isCorrect
                        ? null
                        : $this->taoGhiChuLoi($loiAmDau, $loiVan, $loiThanh),
                    'ngay_tao' => $phien->ngay_tao,
                ]);
            }
        }
    }

    private function giaLapLoiPhatAm($tuChuan, $loiAmDau, $loiVan, $loiThanh): string
    {
        $tu = mb_strtolower($tuChuan);

        if ($loiAmDau) {
            $map = ['l' => 'n', 'n' => 'l', 'tr' => 'ch', 'ch' => 'tr', 's' => 'x', 'x' => 's', 'r' => 'g'];
            foreach ($map as $key => $val) {
                if (str_starts_with($tu, $key)) {
                    return preg_replace('/^' . $key . '/', $val, $tu);
                }
            }
        }

        if ($loiVan) {
            if (str_ends_with($tu, 'ang')) return str_replace('ang', 'an', $tu);
            if (str_ends_with($tu, 'an')) return str_replace('an', 'ang', $tu);
            if (str_ends_with($tu, 'anh')) return str_replace('anh', 'an', $tu);
        }

        if ($loiThanh) {
            return $tu . " (sai thanh điệu)";
        }

        return $tu;
    }

    private function taoGhiChuLoi($amDau, $van, $thanh): string
    {
        if ($amDau) return 'Lỗi âm đầu: Chú ý vị trí đặt lưỡi.';
        if ($van) return 'Lỗi vần: Chú ý kết thúc âm bằng môi hoặc mũi.';
        return 'Lỗi thanh điệu: Cần phân biệt rõ độ cao (hỏi/ngã/sắc).';
    }

    private function saiGanDung(string $tuChuan): string
    {
        $map = [
            'l' => 'n',
            'n' => 'l',
            's' => 'x',
            'x' => 's',
        ];
        $lower = mb_strtolower($tuChuan);
        $first = mb_substr($lower, 0, 1);
        if (isset($map[$first])) {
            return $map[$first] . mb_substr($tuChuan, 1);
        }

        return 'Từ gần giống «' . $tuChuan . '»';
    }
}
