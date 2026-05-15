<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoTrinhTraPhi extends Model
{
    protected $table = 'lo_trinh_tra_phis';

    public const TRANG_THAI_CHO_DUYET = 0;

    public const TRANG_THAI_DA_DUYET = 1;

    public const TRANG_THAI_TU_CHOI = 2;

    protected $fillable = [
        'lo_trinh_id',
        'gia',
        'mo_ta_ban',
        'trang_thai',
        'ngay_duyet',
    ];

    protected function casts(): array
    {
        return [
            'gia' => 'integer',
            'trang_thai' => 'integer',
            'ngay_duyet' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<LoTrinhCaNhan, $this>
     */
    public function loTrinh(): BelongsTo
    {
        return $this->belongsTo(LoTrinhCaNhan::class, 'lo_trinh_id');
    }
}
