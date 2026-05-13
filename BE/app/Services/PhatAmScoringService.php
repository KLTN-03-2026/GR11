<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhatAmScoringService
{
    /**
     * Gọi Python AI chấm phát âm; trả về mảng JSON từ service (diem, van_ban_nhan_dien, …).
     *
     * @return array<string, mixed>
     *
     * @throws \RuntimeException
     */
    public function analyze(UploadedFile $audio, string $tuChuan): array
    {
        $pythonUrl = rtrim((string) config('services.python_ai.url', 'http://127.0.0.1:8001'), '/');

        try {
            $aiResponse = Http::timeout(60)
                ->attach('audio', file_get_contents($audio->getRealPath()), $audio->getClientOriginalName())
                ->post("{$pythonUrl}/analyze", [
                    'tu_chuan' => $tuChuan,
                ]);
        } catch (\Throwable $e) {
            Log::error('PhatAmScoringService: Python service unreachable', ['error' => $e->getMessage()]);
            throw new \RuntimeException('AI_SERVICE_UNAVAILABLE');
        }

        if (! $aiResponse->successful()) {
            Log::error('PhatAmScoringService: Python service error', [
                'status' => $aiResponse->status(),
                'body' => $aiResponse->body(),
            ]);
            throw new \RuntimeException('AI_SERVICE_ERROR');
        }

        /** @var array<string, mixed> $payload */
        $payload = $aiResponse->json();

        return $payload;
    }
}
