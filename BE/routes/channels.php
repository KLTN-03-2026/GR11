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

// Channel public cho admin — mọi admin đang đăng nhập đều nhận sự kiện hồ sơ giáo viên
Broadcast::channel('admin', function ($user): bool {
    return (int) $user->vai_tro_id === NguoiDung::ROLE_ADMIN;
});

// Channel private cho học viên — nhận gợi ý bài học từ giáo viên
Broadcast::channel('student.{studentId}', function ($user, int $studentId): bool {
    return (int) $user->id === $studentId
        && (int) $user->vai_tro_id === NguoiDung::ROLE_USER;
});

