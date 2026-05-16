<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoTrinhCaNhan extends Model
{
    protected $table = 'lo_trinh_ca_nhans';

    protected $fillable = [
        'hoc_vien_id',
        'giao_vien_id',
        'ten_lo_trinh',
    ];

    /**
     * @return BelongsTo<NguoiDung, $this>
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'hoc_vien_id');
    }

    /**
     * @return BelongsTo<NguoiDung, $this>
     */
    public function giaoVien(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'giao_vien_id');
    }

    /**
     * @return HasMany<ChiTietLoTrinh, $this>
     */
    public function chiTiet(): HasMany
    {
        return $this->hasMany(ChiTietLoTrinh::class, 'lo_trinh_id')->orderBy('thu_tu_uu_tien');
    }

    /**
     * @return HasOne<LoTrinhTraPhi, $this>
     */
    public function traPhi(): HasOne
    {
        return $this->hasOne(LoTrinhTraPhi::class, 'lo_trinh_id');
    }

    /**
     * @return HasMany<QuyenLoTrinh, $this>
     */
    public function quyenLoTrinhs(): HasMany
    {
        return $this->hasMany(QuyenLoTrinh::class, 'lo_trinh_id');
    }
}
