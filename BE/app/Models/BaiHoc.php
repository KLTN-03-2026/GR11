<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BaiHoc extends Model
{
    protected $table = 'bai_hocs';

    /** Đã duyệt — hiển thị cho học viên, đủ điều kiện tạo đề kiểm tra (public + teacher quiz). */
    public const TRANG_THAI_HOAT_DONG = 0;

    /** Chờ admin duyệt (giáo viên tạo mới hoặc sửa lại nội dung). */
    public const TRANG_THAI_CHO_DUYET = 1;

    /** Admin từ chối. */
    public const TRANG_THAI_TU_CHOI = 2;

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
     * @return BelongsTo<NguoiDung, $this>
     */
    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }

    /**
     * @return HasMany<TuVung, $this>
     */
    public function tuVungs(): HasMany
    {
        return $this->hasMany(TuVung::class, 'bai_hoc_id')->orderBy('thu_tu');
    }

    /**
     * @return HasOne<BaiKiemTra, $this>
     */
    public function baiKiemTra(): HasOne
    {
        return $this->hasOne(BaiKiemTra::class, 'bai_hoc_id');
    }
}
