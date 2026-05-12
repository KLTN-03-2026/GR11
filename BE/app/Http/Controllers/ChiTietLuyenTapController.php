<?php

namespace App\Http\Controllers;

use App\Models\ChiTietLuyenTap;
use App\Models\LichSuLoiPhatAm;
use App\Models\PhienLuyenTap;
use App\Models\TuVung;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChiTietLuyenTapController extends Controller
{
    /**
     * Nhận file ghi âm, forward sang Python AI service để chấm điểm phát âm,
     * sau đó lưu kết quả vào chi_tiet_luyen_taps và lich_su_loi_phat_ams.
     */
    public function chamDiemPhatAm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phien_id'   => ['required', 'integer', 'exists:phien_luyen_taps,id'],
            'tu_vung_id' => ['required', 'integer', 'exists:tu_vungs,id'],
            'audio'      => ['required', 'file', 'mimes:webm,ogg,wav,mp3', 'max:10240'],
        ]);

        /** @var \App\Models\NguoiDung $user */
        $user = $request->user();

        // Ensure the session belongs to the current user
        $phien = PhienLuyenTap::findOrFail($validated['phien_id']);
        if ((int) $phien->nguoi_dung_id !== (int) $user->id) {
            return response()->json(['message' => 'Không có quyền truy cập phiên này.'], 403);
        }

        // Retrieve the target word
        $tuVung = TuVung::findOrFail($validated['tu_vung_id']);
        $tuChuan = $tuVung->tu_chuan;

        // Persist the audio file
        $audioFile   = $request->file('audio');
        $storagePath = "recordings/{$validated['phien_id']}/{$validated['tu_vung_id']}_" . time() . '.' . $audioFile->getClientOriginalExtension();
        Storage::disk('local')->put($storagePath, file_get_contents($audioFile->getRealPath()));
        $fileUrl = Storage::disk('local')->path($storagePath);

        // Forward to Python AI service
        $pythonUrl = rtrim((string) config('services.python_ai.url', 'http://127.0.0.1:8001'), '/');

        try {
            $aiResponse = Http::timeout(60)
                ->attach('audio', file_get_contents($audioFile->getRealPath()), $audioFile->getClientOriginalName())
                ->post("{$pythonUrl}/analyze", [
                    'tu_chuan' => $tuChuan,
                ]);
        } catch (\Throwable $e) {
            Log::error('Cham diem phat am: Python service unreachable', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'AI service không phản hồi. Vui lòng thử lại sau.'], 503);
        }

        if (! $aiResponse->successful()) {
            Log::error('Cham diem phat am: Python service error', [
                'status' => $aiResponse->status(),
                'body'   => $aiResponse->body(),
            ]);
            return response()->json(['message' => 'AI service trả về lỗi. Vui lòng thử lại sau.'], 502);
        }

        $ai = $aiResponse->json();

        // Persist to chi_tiet_luyen_taps
        $chiTiet = ChiTietLuyenTap::create([
            'phien_id'           => $validated['phien_id'],
            'tu_vung_id'         => $validated['tu_vung_id'],
            'file_ghi_am_url'    => $storagePath,
            'van_ban_ai_nhan_dien' => $ai['van_ban_nhan_dien'] ?? null,
            'diem_tin_cay'       => $ai['diem_tin_cay'] ?? null,
            'diem_chinh_xac'     => $ai['diem'] ?? null,
            'loi_am_dau'         => $ai['loi_am_dau'] ?? false,
            'loi_van'            => $ai['loi_van'] ?? false,
            'loi_thanh_dieu'     => $ai['loi_thanh_dieu'] ?? false,
            'chi_tiet_loi'       => isset($ai['chi_tiet']) ? json_encode($ai['chi_tiet'], JSON_UNESCAPED_UNICODE) : null,
        ]);

        // Upsert error history for each error type that occurred
        $loaiLois = [
            'am_dau'     => $ai['loi_am_dau'] ?? false,
            'van'        => $ai['loi_van'] ?? false,
            'thanh_dieu' => $ai['loi_thanh_dieu'] ?? false,
        ];

        $now = now();
        foreach ($loaiLois as $loaiLoi => $coLoi) {
            if (! $coLoi) {
                continue;
            }

            $chiTietLoiJson = isset($ai['chi_tiet']) ? json_encode($ai['chi_tiet'], JSON_UNESCAPED_UNICODE) : null;

            $existing = LichSuLoiPhatAm::where('nguoi_dung_id', $user->id)
                ->where('tu_vung_id', $validated['tu_vung_id'])
                ->where('loai_loi', $loaiLoi)
                ->first();

            if ($existing) {
                $existing->update([
                    'phien_id'            => $validated['phien_id'],
                    'so_lan_mac_loi'      => $existing->so_lan_mac_loi + 1,
                    'lan_mac_loi_gan_nhat' => $now,
                    'chi_tiet_loi'        => $chiTietLoiJson,
                ]);
            } else {
                LichSuLoiPhatAm::create([
                    'nguoi_dung_id'       => $user->id,
                    'tu_vung_id'          => $validated['tu_vung_id'],
                    'phien_id'            => $validated['phien_id'],
                    'loai_loi'            => $loaiLoi,
                    'so_lan_mac_loi'      => 1,
                    'lan_mac_loi_gan_nhat' => $now,
                    'chi_tiet_loi'        => $chiTietLoiJson,
                    'trang_thai'          => 0,
                ]);
            }
        }

        return response()->json([
            'id'                  => $chiTiet->id,
            'van_ban_nhan_dien'   => $ai['van_ban_nhan_dien'] ?? '',
            'tu_chuan'            => $tuChuan,
            'diem'                => $ai['diem'] ?? 0,
            'diem_tin_cay'        => $ai['diem_tin_cay'] ?? 0,
            'loi_am_dau'          => $ai['loi_am_dau'] ?? false,
            'loi_van'             => $ai['loi_van'] ?? false,
            'loi_thanh_dieu'      => $ai['loi_thanh_dieu'] ?? false,
            'chi_tiet'            => $ai['chi_tiet'] ?? null,
        ], 201);
    }
}
