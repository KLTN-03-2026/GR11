<?php

namespace App\Http\Controllers;

use App\Models\ThongTinHocVien;
use App\Services\StreakRewardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ThongTinHocVienController extends Controller
{
    public function leaderboard(Request $request): JsonResponse
    {
        $type = $request->query('type', 'points');
        $limit = (int) $request->query('limit', 10);
        $limit = $limit > 0 ? $limit : 10;

        $query = ThongTinHocVien::query()
            ->join('nguoi_dungs as u', 'thong_tin_hoc_viens.nguoi_dung_id', '=', 'u.id')
            ->select(
                'thong_tin_hoc_viens.nguoi_dung_id',
                'u.ho_ten',
                'u.email',
                'thong_tin_hoc_viens.diem_tich_luy',
                'thong_tin_hoc_viens.streak_hien_tai'
            );

        if ($type === 'streak') {
            $query->orderByDesc('thong_tin_hoc_viens.streak_hien_tai')
                  ->orderByDesc('thong_tin_hoc_viens.diem_tich_luy');
        } else {
            $query->orderByDesc('thong_tin_hoc_viens.diem_tich_luy')
                  ->orderByDesc('thong_tin_hoc_viens.streak_hien_tai');
        }

        $rows = $query->limit($limit)->get();

        return response()->json([
            'status' => true,
            'data' => $rows,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $tt = ThongTinHocVien::where('nguoi_dung_id', $user->id)->first();
        if (!$tt) {
            return response()->json([
                'status' => true,
                'data' => $this->boSungTrangThaiHomNay(null) + ['nguoi_dung_id' => $user->id],
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $this->boSungTrangThaiHomNay($tt),
        ]);
    }

    public function diemDanh(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $streakData = app(StreakRewardService::class)->capNhatSauHoatDongHoc((int) $user->id, 0);

        if (! empty($streakData['da_diem_danh_truoc_do']) && $streakData['diem_vua_cong'] === 0) {
            return response()->json([
                'status' => true,
                'message' => 'Bạn đã điểm danh hôm nay. Hãy tiếp tục luyện tập để duy trì chuỗi!',
                'data' => $streakData,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Điểm danh thành công! +'.$streakData['diem_vua_cong'].' XP (gồm '.$streakData['diem_thuong_ngay'].' XP thưởng ngày).',
            'data' => $streakData,
        ]);
    }

    /**
     * @param  ThongTinHocVien|object|null  $tt
     * @return array<string, mixed>
     */
    private function boSungTrangThaiHomNay($tt): array
    {
        if (! $tt) {
            return [
                'diem_tich_luy' => 0,
                'streak_hien_tai' => 0,
                'ngay_hoc_cuoi_cung' => null,
                'da_diem_danh_hom_nay' => false,
            ];
        }

        $data = $tt instanceof ThongTinHocVien ? $tt->toArray() : (array) $tt;
        $today = Carbon::today()->toDateString();
        $last = ! empty($data['ngay_hoc_cuoi_cung'])
            ? Carbon::parse($data['ngay_hoc_cuoi_cung'])->toDateString()
            : null;
        $data['da_diem_danh_hom_nay'] = $last === $today;

        return $data;
    }
}
