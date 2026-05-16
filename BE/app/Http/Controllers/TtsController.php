<?php

namespace App\Http\Controllers;

use App\Models\CauHinhHeThong;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TtsController extends Controller
{
    private const FPT_TTS_URL = 'https://api.fpt.ai/hmi/tts/v5';

    /**
     * Trả về âm thanh đọc tiếng Việt (MP3) qua FPT.AI Text-to-Speech v5.
     */
    public function vietnamese(Request $request): Response
    {
        $text = mb_substr(trim((string) $request->query('t', '')), 0, 5000);
        if ($text === '') {
            abort(422);
        }

        $apiKey = (string) config('services.fpt.tts_api_key', '');
        if ($apiKey === '') {
            Log::warning('FPT TTS: thiếu FPT_TTS_API_KEY trong cấu hình.');

            abort(503);
        }

        set_time_limit(120);

        $textForFpt = $this->ensureFptMinimumLength($text);

        $voice = (string) config('services.fpt.tts_voice', 'banmai');
        $speed = (int) config('services.fpt.tts_speed', 0);
        $format = (string) config('services.fpt.tts_format', 'mp3');

        $ttsResponse = Http::withHeaders([
            'api_key' => $apiKey,
            'voice' => $voice,
            'speed' => (string) $speed,
            'format' => $format,
            'Cache-Control' => 'no-cache',
        ])
            ->timeout(30)
            ->withBody($textForFpt, 'text/plain; charset=UTF-8')
            ->post(self::FPT_TTS_URL);

        if (! $ttsResponse->successful()) {
            abort(502);
        }

        $payload = $ttsResponse->json();
        if (! is_array($payload) || (int) ($payload['error'] ?? -1) !== 0) {
            abort(502);
        }

        $asyncUrl = $payload['async'] ?? null;
        if (! is_string($asyncUrl) || $asyncUrl === '') {
            abort(502);
        }

        $audioBody = $this->pollAsyncAudio($asyncUrl);
        if ($audioBody === null) {
            abort(504);
        }

        $this->recordTtsUsage();

        return response($audioBody, 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * FPT yêu cầu tối thiểu 3 ký tự trong body; nối ký tự không phát âm để không đổi nội dung đọc.
     */
    private function ensureFptMinimumLength(string $text): string
    {
        $min = 3;
        $len = mb_strlen($text);
        if ($len >= $min) {
            return $text;
        }

        $pad = str_repeat("\u{200C}", $min - $len);

        return $text.$pad;
    }

    /**
     * File tại URL async có thể chưa sẵn sàng ngay sau response POST.
     */
    private function pollAsyncAudio(string $url): ?string
    {
        $deadline = microtime(true) + 90.0;
        $delayMs = 300;

        while (microtime(true) < $deadline) {
            usleep($delayMs * 1000);
            $delayMs = min(2000, (int) ($delayMs * 1.25));

            $get = Http::withHeaders([
                'User-Agent' => 'Laravel-TTS-Client/1.0',
            ])
                ->timeout(20)
                ->get($url);

            if (! $get->successful()) {
                continue;
            }

            $body = $get->body();
            if ($body === '' || strlen($body) < 32) {
                continue;
            }

            if ($this->looksLikeMp3Binary($body)) {
                return $body;
            }
        }

        return null;
    }

    private function looksLikeMp3Binary(string $body): bool
    {
        if (str_starts_with($body, '<') || str_contains(substr($body, 0, 200), '<!DOCTYPE')) {
            return false;
        }

        if (str_starts_with($body, 'ID3')) {
            return true;
        }

        $b0 = ord($body[0]);
        $b1 = ord($body[1]);

        return $b0 === 0xFF && ($b1 & 0xE0) === 0xE0;
    }

    private function recordTtsUsage(): void
    {
        try {
            $currentMonth = Carbon::now()->format('Y-m');

            DB::transaction(function () use ($currentMonth): void {
                $config = CauHinhHeThong::query()
                    ->where('ma_cau_hinh', 'ai')
                    ->lockForUpdate()
                    ->first();

                $data = is_array($config?->du_lieu) ? $config->du_lieu : [];
                $quota = is_array($data['speech_to_text'] ?? null) ? $data['speech_to_text'] : [];

                $usageMonth = (string) ($quota['usage_month'] ?? '');
                $currentUsage = (int) ($quota['current_usage'] ?? 0);

                if ($usageMonth !== $currentMonth) {
                    $currentUsage = 0;
                }

                $quota['current_usage'] = $currentUsage + 1;
                $quota['usage_month'] = $currentMonth;
                $data['speech_to_text'] = $quota;

                CauHinhHeThong::query()->updateOrCreate(
                    ['ma_cau_hinh' => 'ai'],
                    ['du_lieu' => $data]
                );
            });
        } catch (\Throwable $e) {
            Log::warning('TTS usage tracking failed', ['message' => $e->getMessage()]);
        }
    }
}
