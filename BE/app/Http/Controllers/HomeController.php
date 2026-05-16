<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;

class HomeController extends Controller
{
    public function dataOpen()
    {
        $categories = DanhMucBaiHoc::query()
            ->where('trang_thai', DanhMucBaiHoc::TRANG_THAI_HIEN_THI)
            ->orderBy('thu_tu')
            ->withCount([
                'baiHocs as so_luong_bai_hoc' => function ($query): void {
                    $query->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG);
                },
            ])
            ->with([
                'baiHocs' => function ($query) {
                    $query->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)
                        ->orderBy('thu_tu')
                        ->select(
                            'id',
                            'danh_muc_id',
                            'nguoi_tao_id',
                            'tieu_de',
                            'mo_ta',
                            'cap_do',
                            'thu_tu',
                            'created_at'
                        );
                },
            ])
            ->get(['id', 'ten_danh_muc', 'slug_danh_muc', 'mo_ta', 'thu_tu']);

        $featuredCategories = $categories
            ->take(4)
            ->values()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'ten_danh_muc' => $category->ten_danh_muc,
                    'slug_danh_muc' => $category->slug_danh_muc,
                    'mo_ta' => $category->mo_ta,
                    'so_luong_bai_hoc' => $category->so_luong_bai_hoc,
                ];
            });

        $featuredLessons = $categories
            ->flatMap(function ($category) {
                return $category->baiHocs->map(function ($lesson) use ($category) {
                    return [
                        'id' => $lesson->id,
                        'danh_muc_id' => $category->id,
                        'ten_danh_muc' => $category->ten_danh_muc,
                        'slug_danh_muc' => $category->slug_danh_muc,
                        'tieu_de' => $lesson->tieu_de,
                        'mo_ta' => $lesson->mo_ta,
                        'cap_do' => $lesson->cap_do,
                    ];
                });
            })
            ->take(4)
            ->values();

        return response()->json([
            'data' => $categories,
            'featured_categories' => $featuredCategories,
            'featured_lessons' => $featuredLessons,
            'meta' => [
                'total_categories' => $categories->count(),
                'total_lessons' => $categories->sum('so_luong_bai_hoc'),
            ],
        ]);
    }
}
