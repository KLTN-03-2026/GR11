<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CauHoiKiemTra extends Model
{
    protected $table = 'cau_hoi_kiem_tras';

    protected $fillable = [
        'bai_kiem_tra_id',
        'tu_vung_id',
        'loai',
        'thu_tu',
        'noi_dung_cau',
        'diem_toi_da',
    ];

    protected function casts(): array
    {
        return [
            'thu_tu' => 'integer',
            'diem_toi_da' => 'integer',
        ];
    }

    public function baiKiemTra(): BelongsTo
    {
        return $this->belongsTo(BaiKiemTra::class, 'bai_kiem_tra_id');
    }

    public function tuVung(): BelongsTo
    {
        return $this->belongsTo(TuVung::class, 'tu_vung_id');
    }

    public function luaChons(): HasMany
    {
        return $this->hasMany(LuaChonCauHoi::class, 'cau_hoi_kiem_tra_id')->orderBy('thu_tu');
    }
}
