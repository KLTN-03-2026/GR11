<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuaChonCauHoi extends Model
{
    protected $table = 'lua_chon_cau_hois';

    protected $fillable = [
        'cau_hoi_kiem_tra_id',
        'noi_dung',
        'la_dung',
        'thu_tu',
    ];

    protected function casts(): array
    {
        return [
            'la_dung' => 'boolean',
            'thu_tu' => 'integer',
        ];
    }

    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(CauHoiKiemTra::class, 'cau_hoi_kiem_tra_id');
    }
}
