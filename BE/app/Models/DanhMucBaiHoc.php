<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMucBaiHoc extends Model
{
    protected $table = 'danh_muc_bai_hocs';

    protected $fillable = [
        'ten_danh_muc',
        'slug_danh_muc',
        'mo_ta',
        'trang_thai',
        'thu_tu',
    ];

    /**
     * @return HasMany<BaiHoc, $this>
     */
    public function baiHocs(): HasMany
    {
        return $this->hasMany(BaiHoc::class, 'danh_muc_id');
    }
}
