<?php

namespace App\Events;

use App\Models\BaiHoc;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GiaoVienGuiGoiY implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $hocVienId;
    public int $giaoVienId;
    public string $tenGiaoVien;
    public BaiHoc $baiHoc;
    public string $uuTien;
    public ?string $loiNhan;

    public function __construct(
        int $hocVienId,
        int $giaoVienId,
        string $tenGiaoVien,
        BaiHoc $baiHoc,
        string $uuTien,
        ?string $loiNhan
    ) {
        $this->hocVienId   = $hocVienId;
        $this->giaoVienId  = $giaoVienId;
        $this->tenGiaoVien = $tenGiaoVien;
        $this->baiHoc      = $baiHoc;
        $this->uuTien      = $uuTien;
        $this->loiNhan     = $loiNhan;
    }

    public function broadcastOn(): array
    {
        // Broadcast riêng tới channel của học viên được gợi ý
        return [
            new PrivateChannel('student.' . $this->hocVienId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'bai_hoc_id'   => $this->baiHoc->id,
            'tieu_de'      => $this->baiHoc->tieu_de,
            'ten_giao_vien' => $this->tenGiaoVien,
            'uu_tien'      => $this->uuTien,
            'loi_nhan'     => $this->loiNhan,
            'thoi_gian'    => now()->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'GiaoVienGuiGoiY';
    }
}
