<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichSuLoiPhatAm extends Model
{
    protected $table = 'lich_su_loi_phat_ams';

    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'tu_vung_id',
        'phien_id',
        'loai_loi',
        'so_lan_mac_loi',
        'lan_mac_loi_gan_nhat',
        'chi_tiet_loi',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
    ];

    protected $casts = [
        'lan_mac_loi_gan_nhat' => 'datetime',
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

    /**
     * @return BelongsTo<PhienLuyenTap, $this>
     */
    public function phienLuyenTap(): BelongsTo
    {
        return $this->belongsTo(PhienLuyenTap::class, 'phien_id');
    }
}
