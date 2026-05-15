<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuyenLoTrinh extends Model
{
    protected $table = 'quyen_lo_trinhs';

    public $timestamps = false;

    protected $fillable = [
        'hoc_vien_id',
        'lo_trinh_id',
        'gia_da_mua',
        'ti_le_hoa_hong_da_ap',
        'so_tien_giao_vien_nhan',
        'ngay_mua',
    ];

    protected function casts(): array
    {
        return [
            'gia_da_mua' => 'integer',
            'ti_le_hoa_hong_da_ap' => 'float',
            'so_tien_giao_vien_nhan' => 'integer',
            'ngay_mua' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<NguoiDung, $this>
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'hoc_vien_id');
    }

    /**
     * @return BelongsTo<LoTrinhCaNhan, $this>
     */
    public function loTrinh(): BelongsTo
    {
        return $this->belongsTo(LoTrinhCaNhan::class, 'lo_trinh_id');
    }
}
