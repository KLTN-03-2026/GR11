<?php

use App\Models\ChatSession;
use App\Models\NguoiDung;
use App\Models\QuanHeGvHv;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-session.{sessionId}', function ($user, int $sessionId): bool {
    $session = ChatSession::query()->find($sessionId);
    if (!$session) {
        return false;
    }

    if ((int) $session->user_id === (int) $user->id) {
        return true;
    }

    if ((int) $user->vai_tro_id !== NguoiDung::ROLE_TEACHER) {
        return false;
    }

    return QuanHeGvHv::query()
        ->where('giao_vien_id', $user->id)
        ->where('hoc_vien_id', $session->user_id)
        ->exists();
});

Broadcast::channel('teacher.{teacherId}', function ($user, int $teacherId): bool {
    return (int) $user->id === $teacherId
        && (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER;
});

