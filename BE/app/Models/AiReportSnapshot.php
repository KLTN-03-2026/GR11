<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiReportSnapshot extends Model
{
    protected $table = 'ai_report_snapshots';

    protected $fillable = [
        'nguoi_dung_id',
        'loai_bao_cao',
        'tu_ngay',
        'den_ngay',
        'payload_json',
        'file_path',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'tu_ngay' => 'date',
            'den_ngay' => 'date',
            'payload_json' => 'array',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
