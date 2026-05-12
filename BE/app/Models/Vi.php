<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vi extends Model
{
    protected $table = 'vis';

    protected $fillable = [
        'nguoi_dung_id',
        'so_du',
    ];

    protected $casts = [
        'so_du' => 'integer',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function giaoDichs(): HasMany
    {
        return $this->hasMany(GiaoDichVi::class, 'vi_id');
    }
}
