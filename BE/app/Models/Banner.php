<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'image',
        'link',
        'is_active',
        'thu_tu',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'thu_tu' => 'integer',
    ];
}
