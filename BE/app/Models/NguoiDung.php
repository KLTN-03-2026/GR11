<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'nguoi_dungs';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'sdt',
        'vai_tro_id',
        'ngay_sinh',
        'anh_dai_dien',
        'trang_thai',
        'ngay_tao',
        'is_block',
        'type_account',
        'content_block',
        'hash_active',
        'hash_reset',
    ];
    const ROLE_ADMIN = 1;
    const ROLE_TEACHER = 2;
    const ROLE_USER = 3;

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }
}
