<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietLuyenTap extends Model
{
    protected $table = 'chi_tiet_luyen_taps';

    protected $fillable = [
        'phien_id',
        'tu_vung_id',
        'file_ghi_am_url',
        'van_ban_ai_nhan_dien',
        'diem_tin_cay',
        'diem_chinh_xac',
        'loi_am_dau',
        'loi_van',
        'loi_thanh_dieu',
        'chi_tiet_loi',
    ];

    protected function casts(): array
    {
        return [
            'diem_tin_cay' => 'float',
            'loi_am_dau' => 'boolean',
            'loi_van' => 'boolean',
            'loi_thanh_dieu' => 'boolean',
        ];
    }
}
