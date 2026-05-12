<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoTrinhCaNhan extends Model
{
    protected $table = 'lo_trinh_ca_nhans';

    protected $fillable = [
        'hoc_vien_id',
        'giao_vien_id',
        'ten_lo_trinh',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
