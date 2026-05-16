<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhienKiemTra extends Model
{
    protected $table = 'phien_kiem_tras';

    protected $fillable = [
        'nguoi_dung_id',
        'bai_kiem_tra_id',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'tong_diem',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_bat_dau' => 'datetime',
            'thoi_gian_ket_thuc' => 'datetime',
            'tong_diem' => 'integer',
            'trang_thai' => 'integer',
        ];
    }

    public const TRANG_THAI_DANG_LAM = 0;

    public const TRANG_THAI_NOP = 1;

    public const TRANG_THAI_HET_GIO = 2;

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function baiKiemTra(): BelongsTo
    {
        return $this->belongsTo(BaiKiemTra::class, 'bai_kiem_tra_id');
    }

    public function chiTiets(): HasMany
    {
        return $this->hasMany(ChiTietPhienKiemTra::class, 'phien_kiem_tra_id');
    }
}
