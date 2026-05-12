<?php

namespace App\Http\Controllers;

use App\Http\Requests\HoanThanhPhienRequest;
use App\Http\Requests\StartPhienRequest;
use App\Http\Requests\EndPhienRequest;
use App\Models\PhienLuyenTap;
use App\Models\ThongTinHocVien;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhienLuyenTapController extends Controller
{
    private function resolvePhienByUser(int $phienId, int $userId): array
    {
        $phien = PhienLuyenTap::find($phienId);
        if (!$phien) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy phiên luyện tập.',
                ], 404),
            ];
        }

        if ((int) $phien->nguoi_dung_id !== $userId) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền với phiên này.',
                ], 401),
            ];
        }

        return ['ok' => true, 'phien' => $phien];
    }

    private function closePhienIfNeeded(PhienLuyenTap $phien): void
    {
        if (!$phien->thoi_gian_ket_thuc) {
            $phien->thoi_gian_ket_thuc = now();
            $phien->save();
        }
    }

    public function start(StartPhienRequest $request): JsonResponse
    {
        $user = $request->user();

        $phien = PhienLuyenTap::create([
            'nguoi_dung_id' => $user->id,
            'bai_hoc_id' => (int) $request->input('bai_hoc_id'),
            'thoi_gian_bat_dau' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã bắt đầu phiên luyện tập.',
            'data' => ['phien_id' => $phien->id],
        ]);
    }

    public function end(EndPhienRequest $request): JsonResponse
    {
        $user = $request->user();
        $resolved = $this->resolvePhienByUser((int) $request->input('phien_id'), (int) $user->id);
        if (!$resolved['ok']) {
            return $resolved['response'];
        }

        $phien = $resolved['phien'];
        $this->closePhienIfNeeded($phien);

        return response()->json([
            'status' => true,
            'message' => 'Đã kết thúc phiên luyện tập.',
            'data' => ['phien_id' => $phien->id],
        ]);
    }

    public function hoanThanh(HoanThanhPhienRequest $request): JsonResponse
    {
        $user = $request->user();
        $resolved = $this->resolvePhienByUser((int) $request->input('phien_id'), (int) $user->id);
        if (!$resolved['ok']) {
            return $resolved['response'];
        }
        $phien = $resolved['phien'];

        $tongDiem = (int) $request->input('tong_diem', 0);

        if ($phien->tong_diem !== null) {
            $tt = ThongTinHocVien::where('nguoi_dung_id', $phien->nguoi_dung_id)->first();

            return response()->json([
                'status' => true,
                'message' => 'Phiên đã được hoàn thành trước đó.',
                'data' => [
                    'streak_hien_tai' => (int) ($tt->streak_hien_tai ?? 0),
                    'diem_tich_luy' => (int) ($tt->diem_tich_luy ?? 0),
                    'ngay_hoc_cuoi_cung' => $tt->ngay_hoc_cuoi_cung ?? null,
                ],
            ]);
        }

        DB::beginTransaction();
        try {
            $this->closePhienIfNeeded($phien);
            $phien->tong_diem = $tongDiem;
            $phien->save();

            $tt = ThongTinHocVien::firstOrCreate(
                ['nguoi_dung_id' => $phien->nguoi_dung_id],
                ['diem_tich_luy' => 0, 'streak_hien_tai' => 0, 'ngay_hoc_cuoi_cung' => null]
            );

            $today = Carbon::today()->toDateString();
            $last = $tt->ngay_hoc_cuoi_cung ? Carbon::parse($tt->ngay_hoc_cuoi_cung)->toDateString() : null;

            if ($last === $today) {
                // Đã ghi nhận hôm nay => chỉ cộng điểm
                $tt->diem_tich_luy = (int) $tt->diem_tich_luy + $tongDiem;
            } elseif ($last && Carbon::parse($last)->addDay()->toDateString() === $today) {
                // Học liên tiếp
                $tt->streak_hien_tai = (int) $tt->streak_hien_tai + 1;
                $tt->diem_tich_luy = (int) $tt->diem_tich_luy + $tongDiem;
            } else {
                // Reset streak
                $tt->streak_hien_tai = 1;
                $tt->diem_tich_luy = (int) $tt->diem_tich_luy + $tongDiem;
            }

            $tt->ngay_hoc_cuoi_cung = $today;
            $tt->save();

            // --- Upsert tiến độ bài học ---
            $baiHocId  = $phien->bai_hoc_id;
            $hocVienId = $phien->nguoi_dung_id;

            // Tất cả phiên của học viên trong bài học này
            $allPhienIds = DB::table('phien_luyen_taps')
                ->where('nguoi_dung_id', $hocVienId)
                ->where('bai_hoc_id', $baiHocId)
                ->pluck('id');

            // Số từ distinct đã luyện tập (tích lũy qua mọi phiên)
            $soTuDaHoc = DB::table('chi_tiet_luyen_taps')
                ->whereIn('phien_id', $allPhienIds)
                ->distinct()
                ->count('tu_vung_id');

            // Tổng số từ trong bài học
            $tongTuVung = DB::table('tu_vungs')
                ->where('bai_hoc_id', $baiHocId)
                ->count();

            // Điểm trung bình tích lũy (chỉ tính những lần có điểm)
            $diemTrungBinh = DB::table('chi_tiet_luyen_taps')
                ->whereIn('phien_id', $allPhienIds)
                ->whereNotNull('diem_chinh_xac')
                ->avg('diem_chinh_xac') ?? 0;

            $phanTramHoanThanh = $tongTuVung > 0
                ? min(100, round(($soTuDaHoc / $tongTuVung) * 100, 2))
                : 0;

            $trangThai = $phanTramHoanThanh >= 100 ? 1 : 0;

            TienDoBaiHoc::updateOrCreate(
                ['hoc_vien_id' => $hocVienId, 'bai_hoc_id' => $baiHocId],
                [
                    'so_tu_da_hoc'          => $soTuDaHoc,
                    'phan_tram_hoan_thanh'  => $phanTramHoanThanh,
                    'trang_thai'            => $trangThai,
                    'diem_trung_binh'       => round((float) $diemTrungBinh, 2),
                    'thoi_gian_hoc_cuoi'    => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật streak và điểm thành công.',
                'data' => [
                    'streak_hien_tai' => $tt->streak_hien_tai,
                    'diem_tich_luy' => $tt->diem_tich_luy,
                    'ngay_hoc_cuoi_cung' => $tt->ngay_hoc_cuoi_cung,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật thông tin học viên.',
            ], 500);
        }
    }
}
