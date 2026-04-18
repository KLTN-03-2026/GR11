<?php

namespace App\Http\Controllers;

use App\Models\DiemDanhLoi;
use App\Models\LichSuLoiPhatAm;
use App\Models\TuVung;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Get all bookmarks of the authenticated user
     */
    public function getAllBookmarks(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $bookmarks = DiemDanhLoi::query()
            ->where('nguoi_dung_id', $user->id)
            ->with([
                'tuVung' => function ($q): void {
                    $q->select('id', 'tu_chuan', 'phien_am', 'hinh_anh_url', 'am_thanh_mau_url');
                },
                'baiHoc' => function ($q): void {
                    $q->select('id', 'tieu_de', 'cap_do');
                },
            ])
            ->orderByDesc('ngay_danh_dau')
            ->paginate(20);

        $data = $bookmarks->map(function ($bookmark) {
            return [
                'id' => $bookmark->id,
                'tu_vung' => [
                    'id' => $bookmark->tuVung->id,
                    'tu_chuan' => $bookmark->tuVung->tu_chuan,
                    'phien_am' => $bookmark->tuVung->phien_am,
                    'hinh_anh_url' => $this->resolveMediaUrl($bookmark->tuVung->hinh_anh_url),
                    'am_thanh_mau_url' => $this->resolveMediaUrl($bookmark->tuVung->am_thanh_mau_url),
                ],
                'bai_hoc' => [
                    'id' => $bookmark->baiHoc->id,
                    'tieu_de' => $bookmark->baiHoc->tieu_de,
                    'cap_do' => $bookmark->baiHoc->cap_do,
                ],
                'muc_do_uu_tien' => $bookmark->muc_do_uu_tien,
                'ghi_chu' => $bookmark->ghi_chu,
                'da_hoan_thanh' => $bookmark->da_hoan_thanh,
                'ngay_danh_dau' => $bookmark->ngay_danh_dau,
                'ngay_hoan_thanh' => $bookmark->ngay_hoan_thanh,
                'ngay_tao' => $bookmark->ngay_tao,
                'ngay_cap_nhat' => $bookmark->ngay_cap_nhat,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $bookmarks->currentPage(),
                'total' => $bookmarks->total(),
                'per_page' => $bookmarks->perPage(),
                'last_page' => $bookmarks->lastPage(),
            ],
        ]);
    }

    /**
     * Create a new bookmark
     */
    public function createBookmark(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $validated = $request->validate([
            'tu_vung_id' => ['required', 'integer', 'exists:tu_vungs,id'],
            'bai_hoc_id' => ['required', 'integer', 'exists:bai_hocs,id'],
            'muc_do_uu_tien' => ['nullable', 'string', 'in:thap,binh_thuong,cao'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if bookmark already exists
        $existingBookmark = DiemDanhLoi::where('nguoi_dung_id', $user->id)
            ->where('tu_vung_id', $validated['tu_vung_id'])
            ->first();

        if ($existingBookmark) {
            return response()->json([
                'status' => false,
                'message' => 'Từ này đã được đánh dấu rồi.',
            ], 409);
        }

        $bookmark = DiemDanhLoi::create([
            'nguoi_dung_id' => $user->id,
            'tu_vung_id' => $validated['tu_vung_id'],
            'bai_hoc_id' => $validated['bai_hoc_id'],
            'muc_do_uu_tien' => $validated['muc_do_uu_tien'] ?? 'binh_thuong',
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'da_hoan_thanh' => false,
        ]);

        $bookmark->load([
            'tuVung' => function ($q): void {
                $q->select('id', 'tu_chuan', 'phien_am', 'hinh_anh_url', 'am_thanh_mau_url');
            },
            'baiHoc' => function ($q): void {
                $q->select('id', 'tieu_de', 'cap_do');
            },
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đánh dấu từ vựng thành công.',
            'data' => [
                'id' => $bookmark->id,
                'tu_vung' => [
                    'id' => $bookmark->tuVung->id,
                    'tu_chuan' => $bookmark->tuVung->tu_chuan,
                    'phien_am' => $bookmark->tuVung->phien_am,
                    'hinh_anh_url' => $this->resolveMediaUrl($bookmark->tuVung->hinh_anh_url),
                    'am_thanh_mau_url' => $this->resolveMediaUrl($bookmark->tuVung->am_thanh_mau_url),
                ],
                'bai_hoc' => [
                    'id' => $bookmark->baiHoc->id,
                    'tieu_de' => $bookmark->baiHoc->tieu_de,
                    'cap_do' => $bookmark->baiHoc->cap_do,
                ],
                'muc_do_uu_tien' => $bookmark->muc_do_uu_tien,
                'ghi_chu' => $bookmark->ghi_chu,
                'da_hoan_thanh' => $bookmark->da_hoan_thanh,
                'ngay_danh_dau' => $bookmark->ngay_danh_dau,
            ],
        ]);
    }

    /**
     * Update bookmark (priority, notes)
     */
    public function updateBookmark(Request $request, DiemDanhLoi $bookmark): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if ($bookmark->nguoi_dung_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Không có quyền cập nhật bookmark này.',
            ], 403);
        }

        $validated = $request->validate([
            'muc_do_uu_tien' => ['nullable', 'string', 'in:thap,binh_thuong,cao'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ]);

        if (!empty($validated['muc_do_uu_tien'])) {
            $bookmark->muc_do_uu_tien = $validated['muc_do_uu_tien'];
        }

        if (array_key_exists('ghi_chu', $validated)) {
            $bookmark->ghi_chu = $validated['ghi_chu'];
        }

        $bookmark->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật bookmark thành công.',
            'data' => [
                'id' => $bookmark->id,
                'muc_do_uu_tien' => $bookmark->muc_do_uu_tien,
                'ghi_chu' => $bookmark->ghi_chu,
                'ngay_cap_nhat' => $bookmark->ngay_cap_nhat,
            ],
        ]);
    }

    /**
     * Mark bookmark as completed
     */
    public function markAsCompleted(Request $request, DiemDanhLoi $bookmark): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if ($bookmark->nguoi_dung_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Không có quyền cập nhật bookmark này.',
            ], 403);
        }

        $bookmark->update([
            'da_hoan_thanh' => true,
            'ngay_hoan_thanh' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đánh dấu hoàn thành ôn tập thành công.',
            'data' => [
                'id' => $bookmark->id,
                'da_hoan_thanh' => $bookmark->da_hoan_thanh,
                'ngay_hoan_thanh' => $bookmark->ngay_hoan_thanh,
            ],
        ]);
    }

    /**
     * Delete bookmark
     */
    public function deleteBookmark(Request $request, DiemDanhLoi $bookmark): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if ($bookmark->nguoi_dung_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Không có quyền xóa bookmark này.',
            ], 403);
        }

        $bookmark->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa bookmark thành công.',
        ]);
    }

    /**
     * Get bookmark statistics
     */
    public function getBookmarkStatistics(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $totalBookmarks = DiemDanhLoi::where('nguoi_dung_id', $user->id)->count();
        $completedBookmarks = DiemDanhLoi::where('nguoi_dung_id', $user->id)
            ->where('da_hoan_thanh', true)
            ->count();
        $highPriority = DiemDanhLoi::where('nguoi_dung_id', $user->id)
            ->where('muc_do_uu_tien', 'cao')
            ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'tong_so_bookmark' => $totalBookmarks,
                'da_hoan_thanh' => $completedBookmarks,
                'con_lai' => $totalBookmarks - $completedBookmarks,
                'uu_tien_cao' => $highPriority,
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
