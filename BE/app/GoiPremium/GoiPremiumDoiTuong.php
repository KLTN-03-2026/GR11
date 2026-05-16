<?php

namespace App\GoiPremium;

use App\Models\NguoiDung;

/**
 * Giá trị cột `goi_premiums.doi_tuong` / filter gói theo vai trò người dùng.
 *
 * Ánh xạ với `nguoi_dungs.vai_tro_id` (bảng `vai_tros` do seeder tạo theo thứ tự id):
 * - {@see NguoiDung::ROLE_TEACHER} (2) ↔ {@see self::GiaoVien}
 * - {@see NguoiDung::ROLE_USER} (3) ↔ {@see self::HocVien}
 */
enum GoiPremiumDoiTuong: string
{
    case HocVien = 'hoc_vien';
    case GiaoVien = 'giao_vien';

    public static function tryFromVaiTroId(int $vaiTroId): ?self
    {
        return match ($vaiTroId) {
            NguoiDung::ROLE_TEACHER => self::GiaoVien,
            NguoiDung::ROLE_USER => self::HocVien,
            default => null,
        };
    }
}
