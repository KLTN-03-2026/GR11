<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\QuanHeGvHv;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentSentMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatSession $session;
    public ChatMessage $message;

    public function __construct(ChatSession $session, ChatMessage $message)
    {
        $this->session = $session;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('chat-session.' . $this->session->id),
        ];

        $teacherIds = QuanHeGvHv::query()
            ->where('hoc_vien_id', $this->session->user_id)
            ->pluck('giao_vien_id')
            ->unique()
            ->values();

        foreach ($teacherIds as $teacherId) {
            $channels[] = new PrivateChannel('teacher.' . (int) $teacherId);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'message' => [
                'id' => $this->message->id,
                'role' => $this->message->role,
                'content' => $this->message->content,
                'created_at' => $this->message->created_at->toDateTimeString(),
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'StudentSentMessage';
    }
}
