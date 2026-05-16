<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'session_id',
        'role',
        'content',
        'is_delivered_to_teacher',
        'is_read_by_teacher',
        'is_delivered_to_student',
        'is_read_by_student',
    ];

    protected function casts(): array
    {
        return [
            'is_delivered_to_teacher' => 'boolean',
            'is_read_by_teacher' => 'boolean',
            'is_delivered_to_student' => 'boolean',
            'is_read_by_student' => 'boolean',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }
}
