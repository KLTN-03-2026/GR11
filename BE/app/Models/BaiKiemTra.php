<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaiKiemTra extends Model
{
    protected $table = 'bai_kiem_tras';

    protected $fillable = [
        'bai_hoc_id',
        'nguoi_tao_id',
        'tieu_de',
        'mo_ta_huong_dan',
        'thoi_gian_gioi_han_giay',
        'diem_toi_thieu',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_gioi_han_giay' => 'integer',
            'diem_toi_thieu' => 'integer',
            'trang_thai' => 'integer',
        ];
    }

    public function baiHoc(): BelongsTo
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }

    public function cauHois(): HasMany
    {
        return $this->hasMany(CauHoiKiemTra::class, 'bai_kiem_tra_id')->orderBy('thu_tu');
    }

    public function phienKiemTras(): HasMany
    {
        return $this->hasMany(PhienKiemTra::class, 'bai_kiem_tra_id');
    }
}
