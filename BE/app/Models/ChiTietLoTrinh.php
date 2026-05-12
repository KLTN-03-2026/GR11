<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietLoTrinh extends Model
{
    protected $table = 'chi_tiet_lo_trinhs';

    protected $fillable = [
        'lo_trinh_id',
        'bai_hoc_id',
        'thu_tu_uu_tien',
        'ghi_chu_gv',
    ];
}
