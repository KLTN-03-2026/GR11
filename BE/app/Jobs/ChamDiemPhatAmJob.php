<?php

namespace App\Jobs;

use App\Events\ChamDiemPhatAmCompleted;
use App\Models\ChiTietLuyenTap;
use App\Models\LichSuLoiPhatAm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChamDiemPhatAmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 90;

    public function __construct(
        public string $jobId,
        public int $userId,
        public int $phienId,
        public int $tuVungId,
        public string $storagePath,
        public string $tuChuan,
        public string $originalFilename,
    ) {}

    public function handle(): void
    {
        $cacheKey = $this->cacheKey();

        try {
            if (! Storage::disk('local')->exists($this->storagePath)) {
                throw new \RuntimeException('FILE_NOT_FOUND');
            }

            $absolutePath = Storage::disk('local')->path($this->storagePath);
            $pythonUrl = rtrim((string) config('services.python_ai.url', 'http://127.0.0.1:8001'), '/');

            $aiResponse = Http::timeout(60)
                ->attach('audio', file_get_contents($absolutePath), $this->originalFilename)
                ->post("{$pythonUrl}/analyze", [
                    'tu_chuan' => $this->tuChuan,
                ]);

            if (! $aiResponse->successful()) {
                Log::error('ChamDiemPhatAmJob: Python service error', [
                    'status' => $aiResponse->status(),
                    'body' => $aiResponse->body(),
                ]);
                throw new \RuntimeException('AI_SERVICE_ERROR');
            }

            /** @var array<string, mixed> $ai */
            $ai = $aiResponse->json();
            $result = $this->persistResult($ai);

            Cache::put($cacheKey, [
                'status' => 'completed',
                'user_id' => $this->userId,
                'result' => $result,
            ], now()->addHour());

            event(new ChamDiemPhatAmCompleted($this->userId, $this->jobId, $result));
        } catch (\Throwable $e) {
            Log::error('ChamDiemPhatAmJob failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
            ]);

            $message = match ($e->getMessage()) {
                'AI_SERVICE_ERROR' => 'AI service trả về lỗi. Vui lòng thử lại sau.',
                'FILE_NOT_FOUND' => 'Không tìm thấy file ghi âm.',
                default => 'AI service không phản hồi. Vui lòng thử lại sau.',
            };

            Cache::put($cacheKey, [
                'status' => 'failed',
                'user_id' => $this->userId,
                'message' => $message,
            ], now()->addHour());
        }
    }

    /**
     * @param  array<string, mixed>  $ai
     * @return array<string, mixed>
     */
    private function persistResult(array $ai): array
    {
        $chiTiet = ChiTietLuyenTap::create([
            'phien_id' => $this->phienId,
            'tu_vung_id' => $this->tuVungId,
            'file_ghi_am_url' => $this->storagePath,
            'van_ban_ai_nhan_dien' => $ai['van_ban_nhan_dien'] ?? null,
            'diem_tin_cay' => $ai['diem_tin_cay'] ?? null,
            'diem_chinh_xac' => $ai['diem'] ?? null,
            'loi_am_dau' => $ai['loi_am_dau'] ?? false,
            'loi_van' => $ai['loi_van'] ?? false,
            'loi_thanh_dieu' => $ai['loi_thanh_dieu'] ?? false,
            'chi_tiet_loi' => isset($ai['chi_tiet']) ? json_encode($ai['chi_tiet'], JSON_UNESCAPED_UNICODE) : null,
        ]);

        $loaiLois = [
            'am_dau' => $ai['loi_am_dau'] ?? false,
            'van' => $ai['loi_van'] ?? false,
            'thanh_dieu' => $ai['loi_thanh_dieu'] ?? false,
        ];

        $now = now();
        foreach ($loaiLois as $loaiLoi => $coLoi) {
            if (! $coLoi) {
                continue;
            }

            $chiTietLoiJson = isset($ai['chi_tiet']) ? json_encode($ai['chi_tiet'], JSON_UNESCAPED_UNICODE) : null;

            $existing = LichSuLoiPhatAm::where('nguoi_dung_id', $this->userId)
                ->where('tu_vung_id', $this->tuVungId)
                ->where('loai_loi', $loaiLoi)
                ->first();

            if ($existing) {
                $existing->update([
                    'phien_id' => $this->phienId,
                    'so_lan_mac_loi' => $existing->so_lan_mac_loi + 1,
                    'lan_mac_loi_gan_nhat' => $now,
                    'chi_tiet_loi' => $chiTietLoiJson,
                ]);
            } else {
                LichSuLoiPhatAm::create([
                    'nguoi_dung_id' => $this->userId,
                    'tu_vung_id' => $this->tuVungId,
                    'phien_id' => $this->phienId,
                    'loai_loi' => $loaiLoi,
                    'so_lan_mac_loi' => 1,
                    'lan_mac_loi_gan_nhat' => $now,
                    'chi_tiet_loi' => $chiTietLoiJson,
                    'trang_thai' => 0,
                ]);
            }
        }

        return [
            'id' => $chiTiet->id,
            'van_ban_nhan_dien' => $ai['van_ban_nhan_dien'] ?? '',
            'tu_chuan' => $this->tuChuan,
            'diem' => $ai['diem'] ?? 0,
            'diem_tin_cay' => $ai['diem_tin_cay'] ?? 0,
            'loi_am_dau' => $ai['loi_am_dau'] ?? false,
            'loi_van' => $ai['loi_van'] ?? false,
            'loi_thanh_dieu' => $ai['loi_thanh_dieu'] ?? false,
            'chi_tiet' => $ai['chi_tiet'] ?? null,
        ];
    }

    private function cacheKey(): string
    {
        return 'cham_diem_job:' . $this->jobId;
    }
}
