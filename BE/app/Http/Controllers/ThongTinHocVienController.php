<?php

namespace App\Http\Controllers;

use App\Models\ThongTinHocVien;
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
                'data' => [
                    'nguoi_dung_id' => $user->id,
                    'diem_tich_luy' => 0,
                    'streak_hien_tai' => 0,
                    'ngay_hoc_cuoi_cung' => null,
                ],
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $tt,
        ]);
    }
}
