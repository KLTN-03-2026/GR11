<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauHinhHeThong extends Model
{
    protected $table = 'cau_hinh_he_thongs';

    protected $fillable = [
        'ma_cau_hinh',
        'du_lieu',
    ];

    protected $casts = [
        'du_lieu' => 'array',
    ];
}
