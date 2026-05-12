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
        $ext = strtolower(trim((string) config('vietqr.image_ext', 'png')));

        $addInfo = mb_substr($maDon, 0, 50);

        if ($bankId !== '970423' && $accNo !== '06444982001') {
            $query = http_build_query(array_filter([
                'amount' => $amount > 0 ? $amount : null,
                'addInfo' => $addInfo,
                'accountName' => $accountName !== '' ? $accountName : null,
            ], fn ($v) => $v !== null && $v !== ''));

            return 'https://img.vietqr.io/image/'.$bankId.'-'.$accNo.'-'.$template.'.'.$ext.($query !== '' ? '?'.$query : '');
        }

        $text = "Nạp ví EchoKids\nMã đơn: {$maDon}\nSố tiền: ".number_format($amount, 0, ',', ' ').' VNĐ';

        return 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&ecc=M&data='.rawurlencode($text);
    }
}
