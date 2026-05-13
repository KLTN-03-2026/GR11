<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMucBaiHoc extends Model
{
    protected $table = 'danh_muc_bai_hocs';

    /** Danh mục hiển thị công khai. */
    public const TRANG_THAI_HIEN_THI = 0;

    /** Danh mục tạm ẩn. */
    public const TRANG_THAI_TAM_AN = 1;

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
