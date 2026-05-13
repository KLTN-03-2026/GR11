<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TienDoBaiHoc extends Model
{
    use HasFactory;

    protected $table = 'tien_do_bai_hocs';

    public $timestamps = false;

    protected $fillable = [
        'hoc_vien_id',
        'bai_hoc_id',
        'so_tu_da_hoc',
        'phan_tram_hoan_thanh',
        'trang_thai',
        'diem_trung_binh',
        'thoi_gian_hoc_cuoi',
        'diem_kiem_tra',
        'qua_kiem_tra',
        'thoi_gian_kiem_tra_cuoi',
    ];

    protected $casts = [
        'phan_tram_hoan_thanh' => 'float',
        'diem_trung_binh' => 'float',
        'thoi_gian_hoc_cuoi' => 'datetime',
        'diem_kiem_tra' => 'float',
        'qua_kiem_tra' => 'integer',
        'thoi_gian_kiem_tra_cuoi' => 'datetime',
    ];

    public function hocVien()
    {
        return $this->belongsTo(NguoiDung::class, 'hoc_vien_id');
    }

    public function baiHoc()
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }
}
