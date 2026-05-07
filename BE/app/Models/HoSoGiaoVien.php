<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoGiaoVien extends Model
{
    use HasFactory;

    protected $table = 'ho_so_giao_viens';

    protected $fillable = [
        'nguoi_dung_id',
        'ho_ten',
        'email',
        'so_dien_thoai',
        'anh_dai_dien',
        'cccd_mat_truoc',
        'cccd_mat_sau',
        'bang_cap',
        'chuyen_mon',
        'mo_ta',
        'trang_thai',
        'ghi_chu_admin',
    ];

    // Trạng thái
    const TRANG_THAI_CHO_DUYET = 0;
    const TRANG_THAI_DA_DUYET  = 1;
    const TRANG_THAI_TU_CHOI   = 2;

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
