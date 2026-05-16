<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YeuCauRutTien extends Model
{
    protected $table = 'yeu_cau_rut_tiens';

    protected $fillable = [
        'giao_vien_id',
        'so_tien',
        'ten_ngan_hang_snapshot',
        'so_tai_khoan_snapshot',
        'chu_tai_khoan_snapshot',
        'trang_thai',
        'ly_do_tu_choi',
        'admin_xu_ly_id',
        'ngay_xu_ly',
    ];

    protected $casts = [
        'so_tien' => 'integer',
        'trang_thai' => 'integer',
        'ngay_xu_ly' => 'datetime',
    ];

    /** Cột DB là giao_vien_id nhưng dùng cho mọi người dùng (FK tới nguoi_dungs). */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'giao_vien_id');
    }
}
