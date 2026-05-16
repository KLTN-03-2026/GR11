<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChamDiemPhatAmCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<string, mixed>  $result
     */
    public function __construct(
        public int $userId,
        public string $jobId,
        public array $result,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('student.' . $this->userId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'status' => 'completed',
            'result' => $this->result,
        ];
    }

    public function broadcastAs(): string
    {
        return 'ChamDiemPhatAmCompleted';
    }
}
