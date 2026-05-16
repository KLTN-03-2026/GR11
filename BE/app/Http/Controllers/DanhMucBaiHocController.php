<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use Illuminate\Http\JsonResponse;

class DanhMucBaiHocController extends Controller
{
    public function indexPublic(): JsonResponse
    {
        $items = DanhMucBaiHoc::query()
            ->where('trang_thai', DanhMucBaiHoc::TRANG_THAI_HIEN_THI)
            ->orderBy('thu_tu')
            ->withCount([
                'baiHocs as so_luong_bai_hoc' => function ($query): void {
                    $query->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG);
                },
            ])
            ->get(['id', 'ten_danh_muc', 'slug_danh_muc', 'mo_ta', 'thu_tu']);

        return response()->json([
            'data' => $items,
        ]);
    }
}
