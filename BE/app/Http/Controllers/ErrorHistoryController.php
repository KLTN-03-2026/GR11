<?php

namespace App\Http\Controllers;

use App\Models\LichSuLoiPhatAm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ErrorHistoryController extends Controller
{
    /**
     * Get all errors of the authenticated user
     */
    public function getAllErrors(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $errors = LichSuLoiPhatAm::query()
            ->where('nguoi_dung_id', $user->id)
            ->with([
                'tuVung' => function ($q): void {
                    $q->select('id', 'bai_hoc_id', 'tu_chuan', 'phien_am', 'hinh_anh_url', 'am_thanh_mau_url')
                        ->with([
                            'baiHoc' => function ($qq): void {
                                $qq->select('id', 'tieu_de', 'cap_do');
                            },
                        ]);
                },
            ])
            ->orderByDesc('lan_mac_loi_gan_nhat')
            ->paginate(20);

        $data = $errors->map(function ($error) {
            $tuVung = $error->tuVung;
            $baiHoc = $tuVung?->baiHoc;

            return [
                'id' => $error->id,
                'tu_vung' => [
                    'id' => $tuVung?->id,
                    'tu_chuan' => $tuVung?->tu_chuan,
                    'phien_am' => $tuVung?->phien_am,
                    'hinh_anh_url' => $this->resolveMediaUrl($tuVung?->hinh_anh_url),
                    'am_thanh_mau_url' => $this->resolveMediaUrl($tuVung?->am_thanh_mau_url),
                ],
                'bai_hoc' => [
                    'id' => $baiHoc?->id,
                    'tieu_de' => $baiHoc?->tieu_de,
                    'cap_do' => $baiHoc?->cap_do,
                ],
                'loai_loi' => $error->loai_loi,
                'so_lan_mac_loi' => $error->so_lan_mac_loi,
                'lan_mac_loi_gan_nhat' => $error->lan_mac_loi_gan_nhat,
                'chi_tiet_loi' => $error->chi_tiet_loi,
                'trang_thai' => $error->trang_thai,
                'ngay_tao' => $error->ngay_tao,
                'ngay_cap_nhat' => $error->ngay_cap_nhat,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $errors->currentPage(),
                'total' => $errors->total(),
                'per_page' => $errors->perPage(),
                'last_page' => $errors->lastPage(),
            ],
        ]);
    }

    /**
     * Get errors filtered by status
     */
    public function getErrorsByStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $validated = $request->validate([
            'trang_thai' => ['nullable', 'string', 'in:chua_on_tap,dang_on_tap,da_phat_hien_on_tap'],
            'loai_loi' => ['nullable', 'string', 'in:am_dau,van,thanh_dieu'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = LichSuLoiPhatAm::query()
            ->where('nguoi_dung_id', $user->id)
            ->with([
                'tuVung' => function ($q): void {
                    $q->select('id', 'bai_hoc_id', 'tu_chuan', 'phien_am', 'hinh_anh_url', 'am_thanh_mau_url')
                        ->with([
                            'baiHoc' => function ($qq): void {
                                $qq->select('id', 'tieu_de', 'cap_do');
                            },
                        ]);
                },
            ]);

        if (!empty($validated['trang_thai'])) {
            $query->where('trang_thai', $validated['trang_thai']);
        }

        if (!empty($validated['loai_loi'])) {
            $query->where('loai_loi', $validated['loai_loi']);
        }

        $errors = $query->orderByDesc('lan_mac_loi_gan_nhat')->paginate(20);

        $data = $errors->map(function ($error) {
            $tuVung = $error->tuVung;
            $baiHoc = $tuVung?->baiHoc;

            return [
                'id' => $error->id,
                'tu_vung' => [
                    'id' => $tuVung?->id,
                    'tu_chuan' => $tuVung?->tu_chuan,
                    'phien_am' => $tuVung?->phien_am,
                    'hinh_anh_url' => $this->resolveMediaUrl($tuVung?->hinh_anh_url),
                    'am_thanh_mau_url' => $this->resolveMediaUrl($tuVung?->am_thanh_mau_url),
                ],
                'bai_hoc' => [
                    'id' => $baiHoc?->id,
                    'tieu_de' => $baiHoc?->tieu_de,
                    'cap_do' => $baiHoc?->cap_do,
                ],
                'loai_loi' => $error->loai_loi,
                'so_lan_mac_loi' => $error->so_lan_mac_loi,
                'lan_mac_loi_gan_nhat' => $error->lan_mac_loi_gan_nhat,
                'chi_tiet_loi' => $error->chi_tiet_loi,
                'trang_thai' => $error->trang_thai,
                'ngay_tao' => $error->ngay_tao,
                'ngay_cap_nhat' => $error->ngay_cap_nhat,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $errors->currentPage(),
                'total' => $errors->total(),
                'per_page' => $errors->perPage(),
                'last_page' => $errors->lastPage(),
            ],
        ]);
    }

    /**
     * Update error status
     */
    public function updateErrorStatus(Request $request, LichSuLoiPhatAm $error): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if ($error->nguoi_dung_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Không có quyền cập nhật lỗi này.',
            ], 403);
        }

        $validated = $request->validate([
            'trang_thai' => ['required', 'string', 'in:chua_on_tap,dang_on_tap,da_phat_hien_on_tap'],
        ]);

        $error->update([
            'trang_thai' => $validated['trang_thai'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái thành công.',
            'data' => [
                'id' => $error->id,
                'trang_thai' => $error->trang_thai,
                'ngay_cap_nhat' => $error->ngay_cap_nhat,
            ],
        ]);
    }

    /**
     * Delete error from history
     */
    public function deleteError(Request $request, LichSuLoiPhatAm $error): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if ($error->nguoi_dung_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Không có quyền xóa lỗi này.',
            ], 403);
        }

        $error->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa lỗi thành công.',
        ]);
    }

    /**
     * Get error statistics for dashboard
     */
    public function getErrorStatistics(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $totalErrors = LichSuLoiPhatAm::where('nguoi_dung_id', $user->id)->count();
        $notReviewedErrors = LichSuLoiPhatAm::where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'chua_on_tap')
            ->count();
        $reviewingErrors = LichSuLoiPhatAm::where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'dang_on_tap')
            ->count();
        $reviewedErrors = LichSuLoiPhatAm::where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'da_phat_hien_on_tap')
            ->count();

        $errorsByType = LichSuLoiPhatAm::where('nguoi_dung_id', $user->id)
            ->groupBy('loai_loi')
            ->selectRaw('loai_loi, COUNT(*) as count')
            ->get()
            ->keyBy('loai_loi');

        return response()->json([
            'status' => true,
            'data' => [
                'tong_so_loi' => $totalErrors,
                'chua_on_tap' => $notReviewedErrors,
                'dang_on_tap' => $reviewingErrors,
                'da_phat_hien_on_tap' => $reviewedErrors,
                'loi_theo_loai' => [
                    'am_dau' => $errorsByType['am_dau']->count ?? 0,
                    'van' => $errorsByType['van']->count ?? 0,
                    'thanh_dieu' => $errorsByType['thanh_dieu']->count ?? 0,
                ],
            ],
        ]);
    }

    /**
     * Resolve media URL
     */
    private function resolveMediaUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return asset($path);
    }
}
