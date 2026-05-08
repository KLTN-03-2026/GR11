<?php

namespace App\Events;

use App\Models\HoSoGiaoVien;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GiaoVienNopHoSo implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public HoSoGiaoVien $hoSo;

    public function __construct(HoSoGiaoVien $hoSo)
    {
        $this->hoSo = $hoSo;
    }

    public function broadcastOn(): array
    {
        // Broadcast tới channel admin (public) để mọi admin đang online đều nhận được
        return [
            new Channel('admin'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'ho_so_id'    => $this->hoSo->id,
            'ho_ten'      => $this->hoSo->ho_ten,
            'email'       => $this->hoSo->email,
            'chuyen_mon'  => $this->hoSo->chuyen_mon,
            'trang_thai'  => (int) $this->hoSo->trang_thai,
            'created_at'  => $this->hoSo->created_at?->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'GiaoVienNopHoSo';
    }
}
