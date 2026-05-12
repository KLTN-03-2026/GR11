<?php

namespace App\Services;

use App\Models\DonNapTien;
use App\Models\NguoiDung;
use Illuminate\Support\Str;

class DepositDonService
{
    public function create(NguoiDung $user, int $soTien, ?string $phuongThuc, ?string $ghiChu): array
    {
        $maDon = $this->uniqueMaDon((int) $user->id);

        $meta = json_encode([
            'phuong_thuc' => $phuongThuc,
            'ghi_chu' => $ghiChu,
        ], JSON_UNESCAPED_UNICODE);

        $don = DonNapTien::query()->create([
            'nguoi_dung_id' => $user->id,
            'ma_don' => $maDon,
            'so_tien' => $soTien,
            'trang_thai' => 'cho_thanh_toan',
            'du_lieu_callback' => $meta,
        ]);

        $qrUrl = VietQrService::depositQrImageUrl($maDon, $soTien);

        return [
            'don' => $don,
            'qr_url' => $qrUrl,
            'vietqr_configured' => VietQrService::isConfigured(),
            'qr_account_name' => VietQrService::isConfigured() ? trim((string) config('vietqr.account_name', '')) : '',
        ];
    }

    private function uniqueMaDon(int $userId): string
    {
        for ($i = 0; $i < 15; $i++) {
            $maDon = 'NAP'.now()->format('YmdHis').$userId.strtoupper(Str::random(4));
            if (strlen($maDon) > 50) {
                $maDon = substr($maDon, 0, 50);
            }
            if (! DonNapTien::query()->where('ma_don', $maDon)->exists()) {
                return $maDon;
            }
        }

        throw new \RuntimeException('Không tạo được mã đơn duy nhất.');
    }
}
