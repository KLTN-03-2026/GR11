<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietLoTrinh extends Model
{
    protected $table = 'chi_tiet_lo_trinhs';

    protected $fillable = [
        'lo_trinh_id',
        'bai_hoc_id',
        'thu_tu_uu_tien',
        'ghi_chu_gv',
    ];

    /** @var list<string>|string */
    protected $primaryKey = ['lo_trinh_id', 'bai_hoc_id'];

    public $incrementing = false;

    /**
     * @return BelongsTo<LoTrinhCaNhan, $this>
     */
    public function loTrinh(): BelongsTo
    {
        return $this->belongsTo(LoTrinhCaNhan::class, 'lo_trinh_id');
    }

    /**
     * @return BelongsTo<BaiHoc, $this>
     */
    public function baiHoc(): BelongsTo
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }
}
