<?php

namespace App\Services;

use App\Models\CauHinhHeThong;
use App\Models\GiaoDichVi;
use App\Models\LoTrinhCaNhan;
use App\Models\LoTrinhTraPhi;
use App\Models\NguoiDung;
use App\Models\QuyenLoTrinh;
use App\Models\Vi;
use Illuminate\Support\Facades\DB;

class LoTrinhPurchaseService
{
    public const LOAI_GIAO_DICH_MUA = 'mua_lo_trinh';

    public const LOAI_GIAO_DICH_NHAN_GV = 'nhan_thanh_toan_lo_trinh';

    /**
     * @return array{quyen: QuyenLoTrinh, so_du_sau: int, tien_giao_vien_nhan: int, so_du_giao_vien_sau: int|null, tien_phi_nen_tang: int}
     *
     * @throws \RuntimeException NOT_OWNER | NOT_PAID_ROADMAP | ALREADY_PURCHASED | INSUFFICIENT_BALANCE
     */
    public function mua(NguoiDung $user, LoTrinhCaNhan $loTrinh): array
    {
        if ((int) $loTrinh->hoc_vien_id !== (int) $user->id) {
            throw new \RuntimeException('NOT_OWNER');
        }

        return DB::transaction(function () use ($user, $loTrinh): array {
            $traPhi = LoTrinhTraPhi::query()
                ->where('lo_trinh_id', $loTrinh->id)
                ->lockForUpdate()
                ->first();

            if (! $traPhi || (int) $traPhi->gia <= 0 || (int) $traPhi->trang_thai !== LoTrinhTraPhi::TRANG_THAI_DA_DUYET) {
                throw new \RuntimeException('NOT_PAID_ROADMAP');
            }

            $gia = (int) $traPhi->gia;

            $daCo = QuyenLoTrinh::query()
                ->where('hoc_vien_id', $user->id)
                ->where('lo_trinh_id', $loTrinh->id)
                ->lockForUpdate()
                ->exists();

            if ($daCo) {
                throw new \RuntimeException('ALREADY_PURCHASED');
            }

            $tiLe = $this->docTiLeHoaHongPlatform();
            $tienGiaoVien = (int) floor($gia * (100 - $tiLe) / 100);

            $vi = Vi::query()
                ->where('nguoi_dung_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $vi) {
                Vi::query()->create([
                    'nguoi_dung_id' => $user->id,
                    'so_du' => 0,
                ]);
                $vi = Vi::query()
                    ->where('nguoi_dung_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $truoc = (int) $vi->so_du;
            if ($truoc < $gia) {
                throw new \RuntimeException('INSUFFICIENT_BALANCE');
            }

            $sau = $truoc - $gia;
            $vi->so_du = $sau;
            $vi->save();

            $quyen = QuyenLoTrinh::query()->create([
                'hoc_vien_id' => $user->id,
                'lo_trinh_id' => $loTrinh->id,
                'gia_da_mua' => $gia,
                'ti_le_hoa_hong_da_ap' => $tiLe,
                'so_tien_giao_vien_nhan' => $tienGiaoVien,
                'ngay_mua' => now(),
            ]);

            GiaoDichVi::query()->create([
                'vi_id' => $vi->id,
                'loai_giao_dich' => self::LOAI_GIAO_DICH_MUA,
                'chieu_giao_dich' => 'out',
                'so_tien' => $gia,
                'so_du_truoc' => $truoc,
                'so_du_sau' => $sau,
                'tham_chieu_type' => QuyenLoTrinh::class,
                'tham_chieu_id' => $quyen->id,
                'ghi_chu' => 'Mua lộ trình: '.$loTrinh->ten_lo_trinh,
            ]);

            $tienPhiNenTang = $gia - $tienGiaoVien;
            $soDuGvSau = null;

            $giaoVienId = (int) $loTrinh->giao_vien_id;
            if ($tienGiaoVien > 0 && $giaoVienId > 0 && $giaoVienId !== (int) $user->id) {
                $viGv = Vi::query()
                    ->where('nguoi_dung_id', $giaoVienId)
                    ->lockForUpdate()
                    ->first();

                if (! $viGv) {
                    Vi::query()->create([
                        'nguoi_dung_id' => $giaoVienId,
                        'so_du' => 0,
                    ]);
                    $viGv = Vi::query()
                        ->where('nguoi_dung_id', $giaoVienId)
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                $truocGv = (int) $viGv->so_du;
                $soDuGvSau = $truocGv + $tienGiaoVien;
                $viGv->so_du = $soDuGvSau;
                $viGv->save();

                GiaoDichVi::query()->create([
                    'vi_id' => $viGv->id,
                    'loai_giao_dich' => self::LOAI_GIAO_DICH_NHAN_GV,
                    'chieu_giao_dich' => 'in',
                    'so_tien' => $tienGiaoVien,
                    'so_du_truoc' => $truocGv,
                    'so_du_sau' => $soDuGvSau,
                    'tham_chieu_type' => QuyenLoTrinh::class,
                    'tham_chieu_id' => $quyen->id,
                    'ghi_chu' => 'Nhận thanh toán lộ trình (sau khấu hoa hồng): '.$loTrinh->ten_lo_trinh,
                ]);
            }

            return [
                'quyen' => $quyen,
                'so_du_sau' => $sau,
                'tien_giao_vien_nhan' => $tienGiaoVien,
                'so_du_giao_vien_sau' => $soDuGvSau,
                'tien_phi_nen_tang' => max(0, $tienPhiNenTang),
            ];
        });
    }

    private function docTiLeHoaHongPlatform(): float
    {
        $row = CauHinhHeThong::query()
            ->where('ma_cau_hinh', 'ti_le_hoa_hong_platform')
            ->first();

        $phanTram = (float) ($row?->du_lieu['phan_tram'] ?? 0);
        if ($phanTram < 0) {
            $phanTram = 0;
        }
        if ($phanTram > 100) {
            $phanTram = 100;
        }

        return $phanTram;
    }
}
