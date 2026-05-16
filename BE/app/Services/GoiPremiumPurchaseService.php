<?php

namespace App\Services;

use App\GoiPremium\GoiPremiumDoiTuong;
use App\Models\GiaoDichVi;
use App\Models\GoiNguoiDung;
use App\Models\GoiPremium;
use App\Models\NguoiDung;
use App\Models\Vi;
use Illuminate\Support\Facades\DB;

class GoiPremiumPurchaseService
{
    public const LOAI_GIAO_DICH = 'mua_goi_premium';

    /**
     * Mua gói premium duy nhất theo vai trò: trừ ví, ghi `giao_dich_vis`, tạo `goi_nguoi_dungs`.
     *
     * @return array{goi_nguoi_dung: GoiNguoiDung, so_du_sau: int}
     *
     * @throws \RuntimeException Mã: INVALID_ROLE | NO_PACKAGE | ALREADY_ACTIVE | INSUFFICIENT_BALANCE
     */
    public function mua(NguoiDung $user): array
    {
        $doiTuong = GoiPremiumDoiTuong::tryFromVaiTroId((int) $user->vai_tro_id);
        if ($doiTuong === null) {
            throw new \RuntimeException('INVALID_ROLE');
        }

        return DB::transaction(function () use ($user, $doiTuong): array {
            $goi = GoiPremium::query()
                ->where('doi_tuong', $doiTuong->value)
                ->where('trang_thai', 1)
                ->lockForUpdate()
                ->first();

            if (! $goi) {
                throw new \RuntimeException('NO_PACKAGE');
            }

            $gia = (int) $goi->gia;

            $dangHieuLuc = GoiNguoiDung::query()
                ->where('nguoi_dung_id', $user->id)
                ->where('trang_thai', GoiNguoiDung::TRANG_THAI_HOAT_DONG)
                ->where('ngay_het_han', '>', now())
                ->lockForUpdate()
                ->exists();

            if ($dangHieuLuc) {
                throw new \RuntimeException('ALREADY_ACTIVE');
            }

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

            $now = now();
            $hetHan = $now->copy()->addDays((int) $goi->thoi_han_ngay);

            $goiNguoiDung = GoiNguoiDung::query()->create([
                'nguoi_dung_id' => $user->id,
                'goi_id' => $goi->id,
                'gia_da_mua' => $gia,
                'ngay_kich_hoat' => $now,
                'ngay_het_han' => $hetHan,
                'trang_thai' => GoiNguoiDung::TRANG_THAI_HOAT_DONG,
            ]);

            GiaoDichVi::query()->create([
                'vi_id' => $vi->id,
                'loai_giao_dich' => self::LOAI_GIAO_DICH,
                'chieu_giao_dich' => 'out',
                'so_tien' => $gia,
                'so_du_truoc' => $truoc,
                'so_du_sau' => $sau,
                'tham_chieu_type' => GoiNguoiDung::class,
                'tham_chieu_id' => $goiNguoiDung->id,
                'ghi_chu' => 'Mua gói '.$goi->ten_goi,
            ]);

            return [
                'goi_nguoi_dung' => $goiNguoiDung->fresh(['goiPremium']),
                'so_du_sau' => $sau,
            ];
        });
    }
}
