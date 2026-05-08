<?php

namespace App\Events;

use App\Models\BaiHoc;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GiaoVienNopBaiHoc implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $baiHoc;
    public $teacherName;

    public function __construct(BaiHoc $baiHoc, string $teacherName)
    {
        $this->baiHoc = $baiHoc;
        $this->teacherName = $teacherName;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'GiaoVienNopBaiHoc';
    }
}
