<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaiHoc extends Model
{
    protected $table = 'bai_hocs';

    protected $fillable = [
        'danh_muc_id',
        'nguoi_tao_id',
        'tieu_de',
        'mo_ta',
        'cap_do',
        'thu_tu',
        'trang_thai',
    ];

    /**
     * @return BelongsTo<DanhMucBaiHoc, $this>
     */
    public function danhMuc(): BelongsTo
    {
        return $this->belongsTo(DanhMucBaiHoc::class, 'danh_muc_id');
    }

    /**
     * @return HasMany<TuVung, $this>
     */
    public function tuVungs(): HasMany
    {
        return $this->hasMany(TuVung::class, 'bai_hoc_id')->orderBy('thu_tu');
    }
}
