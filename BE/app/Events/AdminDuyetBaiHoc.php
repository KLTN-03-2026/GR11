<?php

namespace App\Events;

use App\Models\BaiHoc;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminDuyetBaiHoc implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BaiHoc $baiHoc;
    public int $giaoVienId;
    public string $trangThaiLabel;

    public function __construct(BaiHoc $baiHoc, int $giaoVienId, string $trangThaiLabel)
    {
        $this->baiHoc       = $baiHoc;
        $this->giaoVienId   = $giaoVienId;
        $this->trangThaiLabel = $trangThaiLabel;
    }

    public function broadcastOn(): array
    {
        // Broadcast riêng tới channel của giáo viên sở hữu bài học
        return [
            new PrivateChannel('teacher.' . $this->giaoVienId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'bai_hoc_id'    => $this->baiHoc->id,
            'tieu_de'       => $this->baiHoc->tieu_de,
            'trang_thai'    => (int) $this->baiHoc->trang_thai,
            'trang_thai_label' => $this->trangThaiLabel,
            'updated_at'    => $this->baiHoc->updated_at?->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AdminDuyetBaiHoc';
    }
}
