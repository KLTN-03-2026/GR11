<?php

namespace App\Services;

class VietQrService
{
    public static function isConfigured(): bool
    {
        $bankId = trim((string) config('vietqr.bank_id', ''));
        $accNo = preg_replace('/\s+/', '', (string) config('vietqr.account_no', ''));

        return $bankId !== '' && $accNo !== '';
    }

    /**
     * VietQR public image API — no API key.
     *
     * @see https://www.vietqr.io/
     */
    public static function depositQrImageUrl(string $maDon, int $amount): string
    {
        $bankId = trim((string) config('vietqr.bank_id', '970423'));
        $accNo = preg_replace('/\s+/', '', (string) config('vietqr.account_no', '06444982001'));
        $accountName = trim((string) config('vietqr.account_name', 'Nguyen Thanh Nam'));
        $template = trim((string) config('vietqr.template', 'compact2')) ?: 'compact2';
        $ext = strtolower(trim((string) config('vietqr.image_ext', 'png'))) ?: 'png';

        $addInfo = mb_substr($maDon, 0, 50);

        // Luôn dùng ảnh VietQR chuẩn (chuyển khoản vào STK đã cấu hình). Không còn QR
        // api.qrserver.com chỉ mã hoá chữ — tránh nhầm với thanh toán thật.
        $query = http_build_query(array_filter([
            'amount' => $amount > 0 ? $amount : null,
            'addInfo' => $addInfo,
            'accountName' => $accountName !== '' ? $accountName : null,
        ], fn ($v) => $v !== null && $v !== ''));

        return 'https://img.vietqr.io/image/'.$bankId.'-'.$accNo.'-'.$template.'.'.$ext.($query !== '' ? '?'.$query : '');
    }
}
