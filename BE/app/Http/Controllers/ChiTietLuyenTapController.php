<?php

namespace App\Http\Controllers;

use App\Jobs\ChamDiemPhatAmJob;
use App\Models\PhienLuyenTap;
use App\Models\TuVung;
use App\Services\AI\Rag\Support\PremiumFeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChiTietLuyenTapController extends Controller
{
    public function __construct(
        private readonly PremiumFeatureService $premiumFeatureService,
    ) {}

    /**
     * Nhận file ghi âm, đưa vào hàng đợi chấm phát âm (ưu tiên nếu Premium).
     */
    public function chamDiemPhatAm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phien_id' => ['required', 'integer', 'exists:phien_luyen_taps,id'],
            'tu_vung_id' => ['required', 'integer', 'exists:tu_vungs,id'],
            'audio' => ['required', 'file', 'mimes:webm,ogg,wav,mp3', 'max:10240'],
        ]);

        /** @var \App\Models\NguoiDung $user */
        $user = $request->user();

        $phien = PhienLuyenTap::findOrFail($validated['phien_id']);
        if ((int) $phien->nguoi_dung_id !== (int) $user->id) {
            return response()->json(['message' => 'Không có quyền truy cập phiên này.'], 403);
        }

        $tuVung = TuVung::findOrFail($validated['tu_vung_id']);
        $tuChuan = $tuVung->tu_chuan;

        $audioFile = $request->file('audio');
        $storagePath = "recordings/{$validated['phien_id']}/{$validated['tu_vung_id']}_" . time() . '.' . $audioFile->getClientOriginalExtension();
        Storage::disk('local')->put($storagePath, file_get_contents($audioFile->getRealPath()));

        $jobId = (string) Str::uuid();
        $isPremium = $this->premiumFeatureService->hasActivePremium($user);
        $queue = $isPremium ? 'high' : 'default';

        Cache::put('cham_diem_job:' . $jobId, [
            'status' => 'pending',
            'user_id' => $user->id,
        ], now()->addHour());

        ChamDiemPhatAmJob::dispatch(
            $jobId,
            (int) $user->id,
            (int) $validated['phien_id'],
            (int) $validated['tu_vung_id'],
            $storagePath,
            $tuChuan,
            $audioFile->getClientOriginalName(),
        )->onQueue($queue);

        $cached = Cache::get('cham_diem_job:' . $jobId);
        if (is_array($cached) && ($cached['status'] ?? '') === 'completed') {
            return response()->json(array_merge([
                'job_id' => $jobId,
                'status' => 'completed',
            ], $cached['result'] ?? []), 200);
        }

        return response()->json([
            'job_id' => $jobId,
            'status' => 'pending',
            'queue' => $queue,
        ], 202);
    }

    public function ketQuaChamDiem(Request $request, string $jobId): JsonResponse
    {
        /** @var \App\Models\NguoiDung $user */
        $user = $request->user();

        $cached = Cache::get('cham_diem_job:' . $jobId);
        if (! is_array($cached) || (int) ($cached['user_id'] ?? 0) !== (int) $user->id) {
            return response()->json(['message' => 'Không tìm thấy job chấm điểm.'], 404);
        }

        $status = (string) ($cached['status'] ?? 'pending');

        if ($status === 'pending') {
            return response()->json([
                'job_id' => $jobId,
                'status' => 'pending',
            ], 202);
        }

        if ($status === 'failed') {
            return response()->json([
                'job_id' => $jobId,
                'status' => 'failed',
                'message' => $cached['message'] ?? 'Có lỗi xảy ra khi chấm điểm.',
            ], 500);
        }

        return response()->json(array_merge([
            'job_id' => $jobId,
            'status' => 'completed',
        ], $cached['result'] ?? []), 200);
    }
}
