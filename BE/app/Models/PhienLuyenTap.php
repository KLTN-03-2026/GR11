<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhienLuyenTap extends Model
{
    protected $table = 'phien_luyen_taps';

    protected $fillable = [
        'nguoi_dung_id',
        'bai_hoc_id',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'tong_diem',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_bat_dau' => 'datetime',
            'thoi_gian_ket_thuc' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<BaiHoc, $this>
     */
    public function baiHoc(): BelongsTo
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }
}
