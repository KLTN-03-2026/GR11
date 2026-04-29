<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongTinHocVien extends Model
{
    protected $table = 'thong_tin_hoc_viens';

    protected $fillable = [
        'nguoi_dung_id',
        'diem_tich_luy',
        'streak_hien_tai',
        'ngay_hoc_cuoi_cung',
    ];
}
