<?php

namespace App\Http\Controllers;

use App\Events\TeacherSentMessage;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\QuanHeGvHv;
use App\Support\ChatDisplayTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ChatController extends Controller
{
    public function unreadCount(Request $request)
    {
        $teacherId = Auth::id();

        $studentIds = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->pluck('hoc_vien_id');

        $count = ChatMessage::query()
            ->join('chat_sessions as cs', 'cs.id', '=', 'chat_messages.session_id')
            ->whereIn('cs.user_id', $studentIds)
            ->whereNotNull('cs.lesson_id')
            ->where('chat_messages.role', 'user')
            ->where('chat_messages.is_read_by_teacher', false)
            ->count();

        return response()->json([
            'unread_count' => (int) $count,
        ]);
    }

    public function getSessionsForTeacher(Request $request)
    {
        $teacherId = Auth::id();

        // Lấy danh sách học viên của giáo viên
        $studentIds = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->pluck('hoc_vien_id');

        if (Schema::hasColumn('chat_messages', 'is_delivered_to_teacher')) {
            ChatMessage::query()
                ->join('chat_sessions as cs', 'cs.id', '=', 'chat_messages.session_id')
                ->whereIn('cs.user_id', $studentIds)
                ->whereNotNull('cs.lesson_id')
                ->where('chat_messages.role', 'user')
                ->where('chat_messages.is_delivered_to_teacher', false)
                ->update(['chat_messages.is_delivered_to_teacher' => true]);
        }

        // Lấy sessions với học viên đó, có lesson
        $sessions = ChatSession::with(['user', 'lesson', 'messages' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('role', 'user')
                    ->where('is_read_by_teacher', false);
            }])
            ->whereIn('user_id', $studentIds)
            ->whereNotNull('lesson_id')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'studentName' => $session->user->ho_ten,
                    'studentAvatar' => $session->user->anh_dai_dien,
                    // BaiHoc model uses `tieu_de` for the title
                    'lesson' => $session->lesson->tieu_de ?? '',
                    'lastMessage' => $session->messages->first()?->content ?? '',
                    'timestamp' => ChatDisplayTime::format($session->updated_at),
                    'unreadCount' => (int) ($session->unread_count ?? 0),
                    'status' => $session->status,
                ];
            });

        return response()->json($sessions);
    }

    public function getMessages($sessionId)
    {
        $teacherId = Auth::id();

        $session = ChatSession::with(['lesson', 'user'])
            ->find($sessionId);

        if (! $session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Kiểm tra quyền truy cập
        $isTeacherOfStudent = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->where('hoc_vien_id', $session->user_id)
            ->exists();

        if (! $isTeacherOfStudent) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $updatePayload = ['is_read_by_teacher' => true];
        if (Schema::hasColumn('chat_messages', 'is_delivered_to_teacher')) {
            $updatePayload['is_delivered_to_teacher'] = true;
        }

        ChatMessage::where('session_id', $sessionId)
            ->where('role', 'user')
            ->where('is_read_by_teacher', false)
            ->update($updatePayload);

        $studentName = $session->user->ho_ten ?? '';
        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($studentName) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->role === 'teacher' ? 'Giáo viên' : $studentName,
                    'text' => $msg->content,
                    'time' => ChatDisplayTime::format($msg->created_at),
                    'status' => $msg->role === 'user'
                        ? ($msg->is_read_by_teacher ? 'seen' : (($msg->is_delivered_to_teacher ?? false) ? 'delivered' : 'sent'))
                        : (($msg->is_read_by_student ?? false) ? 'seen' : (($msg->is_delivered_to_student ?? false) ? 'delivered' : 'sent')),
                ];
            });

        return response()->json([
            'session' => [
                'id' => $session->id,
                'studentName' => $studentName,
                'studentAvatar' => $session->user->anh_dai_dien,
                'lesson' => $session->lesson->tieu_de ?? '',
            ],
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $teacherId = Auth::id();

        $session = ChatSession::find($sessionId);

        if (! $session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Kiểm tra quyền
        $isTeacherOfStudent = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->where('hoc_vien_id', $session->user_id)
            ->exists();

        if (! $isTeacherOfStudent) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payload = [
            'session_id' => $sessionId,
            'role' => 'teacher',
            'content' => $request->message,
            'is_read_by_teacher' => true,
            'created_at' => now(),
        ];
        if (Schema::hasColumn('chat_messages', 'is_delivered_to_student')) {
            $payload['is_delivered_to_student'] = false;
        }
        if (Schema::hasColumn('chat_messages', 'is_read_by_student')) {
            $payload['is_read_by_student'] = false;
        }

        $message = ChatMessage::create($payload);

        // Cập nhật updated_at của session
        $session->touch();
        event(new TeacherSentMessage($session, $message));

        return response()->json([
            'id' => $message->id,
            'sender' => 'Giáo viên',
            'text' => $message->content,
            'time' => ChatDisplayTime::format($message->created_at),
            'status' => 'sent',
        ]);
    }

    public function deleteSession($sessionId)
    {
        $teacherId = Auth::id();

        $session = ChatSession::with('user')->find($sessionId);
        if (! $session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $isTeacherOfStudent = QuanHeGvHv::where('giao_vien_id', $teacherId)
            ->where('hoc_vien_id', $session->user_id)
            ->exists();
        if (! $isTeacherOfStudent) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa đoạn chat.',
            'session_id' => (int) $sessionId,
        ]);
    }
}
