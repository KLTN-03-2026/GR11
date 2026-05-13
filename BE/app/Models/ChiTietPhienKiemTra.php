<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhienKiemTra extends Model
{
    protected $table = 'chi_tiet_phien_kiem_tras';

    protected $fillable = [
        'phien_kiem_tra_id',
        'cau_hoi_kiem_tra_id',
        'lua_chon_id',
        'file_ghi_am_url',
        'diem_dat',
        'phan_hoi',
    ];

    protected function casts(): array
    {
        return [
            'diem_dat' => 'integer',
            'phan_hoi' => 'array',
        ];
    }

    public function phienKiemTra(): BelongsTo
    {
        return $this->belongsTo(PhienKiemTra::class, 'phien_kiem_tra_id');
    }

    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(CauHoiKiemTra::class, 'cau_hoi_kiem_tra_id');
    }

    public function luaChon(): BelongsTo
    {
        return $this->belongsTo(LuaChonCauHoi::class, 'lua_chon_id');
    }
}
