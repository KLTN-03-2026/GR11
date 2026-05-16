<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoiPremium extends Model
{
    protected $table = 'goi_premiums';

    protected $fillable = [
        'ten_goi',
        'doi_tuong',
        'mo_ta',
        'gia',
        'thoi_han_ngay',
        'tinh_nang',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'gia' => 'integer',
            'thoi_han_ngay' => 'integer',
            'tinh_nang' => 'array',
            'trang_thai' => 'integer',
        ];
    }

    public function goiNguoiDungs(): HasMany
    {
        return $this->hasMany(GoiNguoiDung::class, 'goi_id');
    }
}
