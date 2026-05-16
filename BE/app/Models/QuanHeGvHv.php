<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuanHeGvHv extends Model
{
    protected $table = 'quan_he_gv_hvs';

    /** Đang kết nối / theo dõi (học viên được phép làm đề của GV). */
    public const TRANG_THAI_DANG_KET_NOI = 1;

    protected $fillable = [
        'giao_vien_id',
        'hoc_vien_id',
        'trang_thai',
        'ngay_ket_noi',
    ];
}
