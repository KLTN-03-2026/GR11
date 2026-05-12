<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuChatAi extends Model
{
    protected $table = 'lich_su_chat_ais';

    protected $fillable = [
        'nguoi_dung_id',
        'noi_dung_chat',
        'phan_hoi_ai',
        'bai_hoc_duoc_goi_y_ai',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
