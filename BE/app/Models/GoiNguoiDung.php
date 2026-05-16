<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoiNguoiDung extends Model
{
    public const TRANG_THAI_HET_HAN = 0;

    public const TRANG_THAI_HOAT_DONG = 1;

    protected $table = 'goi_nguoi_dungs';

    protected $fillable = [
        'nguoi_dung_id',
        'goi_id',
        'gia_da_mua',
        'ngay_kich_hoat',
        'ngay_het_han',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'gia_da_mua' => 'integer',
            'trang_thai' => 'integer',
            'ngay_kich_hoat' => 'datetime',
            'ngay_het_han' => 'datetime',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function goiPremium(): BelongsTo
    {
        return $this->belongsTo(GoiPremium::class, 'goi_id');
    }
}
