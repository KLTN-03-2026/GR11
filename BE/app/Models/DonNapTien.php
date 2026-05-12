<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonNapTien extends Model
{
    protected $table = 'don_nap_tiens';

    protected $fillable = [
        'nguoi_dung_id',
        'ma_don',
        'so_tien',
        'trang_thai',
        'ma_giao_dich',
        'ngan_hang',
        'du_lieu_callback',
    ];

    protected $casts = [
        'so_tien' => 'integer',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
