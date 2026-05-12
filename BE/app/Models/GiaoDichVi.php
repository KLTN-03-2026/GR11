<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GiaoDichVi extends Model
{
    protected $table = 'giao_dich_vis';

    protected $fillable = [
        'vi_id',
        'loai_giao_dich',
        'chieu_giao_dich',
        'so_tien',
        'so_du_truoc',
        'so_du_sau',
        'tham_chieu_type',
        'tham_chieu_id',
        'ghi_chu',
    ];

    protected $casts = [
        'so_tien' => 'integer',
        'so_du_truoc' => 'integer',
        'so_du_sau' => 'integer',
    ];

    public function vi(): BelongsTo
    {
        return $this->belongsTo(Vi::class, 'vi_id');
    }

    public function thamChieu(): MorphTo
    {
        return $this->morphTo('tham_chieu', 'tham_chieu_type', 'tham_chieu_id');
    }
}
