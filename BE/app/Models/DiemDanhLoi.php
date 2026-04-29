<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiemDanhLoi extends Model
{
    protected $table = 'diem_danh_lois';

    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'tu_vung_id',
        'muc_do_uu_tien',
        'ghi_chu',
        'da_hoan_thanh',
        'ngay_danh_dau',
        'ngay_hoan_thanh',
        'ngay_tao',
        'ngay_cap_nhat',
    ];

    protected $casts = [
        'da_hoan_thanh' => 'boolean',
        'ngay_danh_dau' => 'datetime',
        'ngay_hoan_thanh' => 'datetime',
        'ngay_tao' => 'datetime',
        'ngay_cap_nhat' => 'datetime',
    ];

    /**
     * @return BelongsTo<NguoiDung, $this>
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    /**
     * @return BelongsTo<TuVung, $this>
     */
    public function tuVung(): BelongsTo
    {
        return $this->belongsTo(TuVung::class, 'tu_vung_id');
    }
}
