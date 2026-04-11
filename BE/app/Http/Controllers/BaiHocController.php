<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaiHocController extends Controller
{
    public function indexPublic(Request $request): JsonResponse
    {
        $query = BaiHoc::query()
            ->with([
                'danhMuc' => function ($q): void {
                    $q->select('id', 'ten_danh_muc', 'slug_danh_muc');
                },
            ])
            ->where('trang_thai', 0)
            ->orderBy('thu_tu');

        $meta = null;

        if ($request->filled('danh_muc_id')) {
            $validated = $request->validate([
                'danh_muc_id' => ['required', 'integer', 'exists:danh_muc_bai_hocs,id'],
            ]);

            $query->where('danh_muc_id', $validated['danh_muc_id']);

            $danhMuc = DanhMucBaiHoc::query()->find($validated['danh_muc_id']);
            if ($danhMuc !== null) {
                $meta = [
                    'danh_muc'    =>  [
                        'id'               =>  $danhMuc->id,
                        'ten_danh_muc'     =>  $danhMuc->ten_danh_muc,
                        'slug_danh_muc'    =>  $danhMuc->slug_danh_muc,
                    ],
                ];
            }
        }

        return response()->json([
            'data'    =>  $query->get(),
            'meta'    =>  $meta,
        ]);
    }

    public function showPublic(BaiHoc $baiHoc): JsonResponse
    {
        if ((int) $baiHoc->trang_thai !== 0) {
            abort(404);
        }

        $baiHoc->load([
            'danhMuc' => function ($q): void {
                $q->select('id', 'ten_danh_muc', 'slug_danh_muc');
            },
            'tuVungs',
        ]);

        $tuVungs = $baiHoc->tuVungs->map(function ($tv) {
            return [
                'id'                 =>  $tv->id,
                'tu_chuan'           =>  $tv->tu_chuan,
                'phien_am'           =>  $tv->phien_am,
                'cap_do'             =>  $tv->cap_do,
                'hinh_anh_url'       =>  $this->resolvePublicMediaUrl($tv->hinh_anh_url),
                'am_thanh_mau_url'   =>  $this->resolvePublicMediaUrl($tv->am_thanh_mau_url),
                'thu_tu'             =>  $tv->thu_tu,
            ];
        });

        return response()->json([
            'data'    =>  [
                'id'          =>  $baiHoc->id,
                'tieu_de'     =>  $baiHoc->tieu_de,
                'mo_ta'       =>  $baiHoc->mo_ta,
                'cap_do'      =>  $baiHoc->cap_do,
                'thu_tu'      =>  $baiHoc->thu_tu,
                'danh_muc'    =>  $baiHoc->danhMuc,
                'tu_vungs'    =>  $tuVungs,
            ],
        ]);
    }

    private function resolvePublicMediaUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }
}
