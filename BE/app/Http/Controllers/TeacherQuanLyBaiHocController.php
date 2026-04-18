<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyDanhMucBaiHocRequest;
use App\Http\Requests\GetBaiHocTheoDanhMucRequest;
use App\Http\Requests\StoreDanhMucBaiHocRequest;
use App\Http\Requests\StoreTeacherBaiHocRequest;
use App\Http\Requests\DestroyTeacherBaiHocRequest;
use App\Http\Requests\UpdateDanhMucBaiHocRequest;
use App\Http\Requests\UpdateTeacherBaiHocRequest;
use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeacherQuanLyBaiHocController extends Controller
{
    public function indexDanhMuc(): JsonResponse
    {
        $data = DanhMucBaiHoc::query()
            ->withCount('baiHocs')
            ->orderBy('thu_tu')
            ->orderByDesc('id')
            ->get()
            ->map(function (DanhMucBaiHoc $dm) {
                return [
                    'id' => $dm->id,
                    'ten' => $dm->ten_danh_muc,
                    'ten_danh_muc' => $dm->ten_danh_muc,
                    'icon' => $this->iconFromMoTa($dm->mo_ta),
                    'so_bai' => (int) $dm->bai_hocs_count,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function storeDanhMuc(StoreDanhMucBaiHocRequest $request): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Giáo viên không có quyền thêm danh mục. Vui lòng liên hệ admin.',
        ], 403);
    }

    public function updateDanhMuc(UpdateDanhMucBaiHocRequest $request, int $id): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Giáo viên không có quyền sửa danh mục. Vui lòng liên hệ admin.',
        ], 403);
    }

    public function destroyDanhMuc(DestroyDanhMucBaiHocRequest $request, int $id): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Giáo viên không có quyền xóa danh mục. Vui lòng liên hệ admin.',
        ], 403);
    }

    public function indexBaiHocTheoDanhMuc(GetBaiHocTheoDanhMucRequest $request, int $id): JsonResponse
    {
        $danhMuc = DanhMucBaiHoc::find($id);
        if (!$danhMuc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy danh mục',
            ], 404);
        }

        $data = BaiHoc::with('tuVungs:id,bai_hoc_id')
            ->where('danh_muc_id', $id)
            ->orderBy('thu_tu')
            ->orderByDesc('id')
            ->get()
            ->map(function (BaiHoc $baiHoc) {
                return [
                    'id' => $baiHoc->id,
                    'danh_muc_id' => $baiHoc->danh_muc_id,
                    'tieu_de' => $baiHoc->tieu_de,
                    'mo_ta' => $baiHoc->mo_ta,
                    'cap_do' => $baiHoc->cap_do,
                    'so_luong_tu' => $baiHoc->tuVungs->count(),
                    'trang_thai' => $this->mapTrangThaiBaiHocToTeacherUi($baiHoc->trang_thai),
                    'trang_thai_so' => (int) $baiHoc->trang_thai,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function storeBaiHoc(StoreTeacherBaiHocRequest $request): JsonResponse
    {
        $user = $request->user();
        $baiHoc = DB::transaction(function () use ($request, $user) {
            $danhMucId = (int) $request->danh_muc_id;
            $thuTuInput = $request->input('thu_tu');
            $thuTu = $this->resolveThuTuKhiThemBaiHoc($danhMucId, $thuTuInput);

            if ($thuTuInput !== null && $thuTuInput !== '') {
                BaiHoc::query()
                    ->where('danh_muc_id', $danhMucId)
                    ->where('thu_tu', '>=', $thuTu)
                    ->update(['thu_tu' => DB::raw('thu_tu + 1')]);
            }

            return BaiHoc::create([
                'danh_muc_id' => $danhMucId,
                'nguoi_tao_id' => $user->id,
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'cap_do' => $request->cap_do,
                'thu_tu' => $thuTu,
                // Giáo viên chỉ có thể gửi bài ở trạng thái chờ duyệt.
                'trang_thai' => 0,
            ]);
        });

        $baiHoc->load('tuVungs:id,bai_hoc_id');

        return response()->json([
            'status' => true,
            'message' => 'Đã tạo bài học thành công',
            'data' => [
                'id' => $baiHoc->id,
                'danh_muc_id' => $baiHoc->danh_muc_id,
                'tieu_de' => $baiHoc->tieu_de,
                'mo_ta' => $baiHoc->mo_ta,
                'cap_do' => $baiHoc->cap_do,
                'so_luong_tu' => $baiHoc->tuVungs->count(),
                'trang_thai' => $this->mapTrangThaiBaiHocToTeacherUi($baiHoc->trang_thai),
                'trang_thai_so' => (int) $baiHoc->trang_thai,
            ],
        ], 201);
    }

    public function updateBaiHoc(UpdateTeacherBaiHocRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::find($id);
        if (!$baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học',
            ], 404);
        }

        DB::transaction(function () use ($request, $baiHoc) {
            $oldDanhMucId = (int) $baiHoc->danh_muc_id;
            $newDanhMucId = (int) $request->danh_muc_id;
            $oldThuTu = (int) $baiHoc->thu_tu;
            $newThuTu = $request->input('thu_tu');
            $newThuTu = $this->resolveThuTuKhiCapNhatBaiHoc($baiHoc, $newDanhMucId, $newThuTu);

            if ($oldDanhMucId !== $newDanhMucId) {
                BaiHoc::query()
                    ->where('danh_muc_id', $oldDanhMucId)
                    ->where('thu_tu', '>', $oldThuTu)
                    ->update(['thu_tu' => DB::raw('thu_tu - 1')]);

                BaiHoc::query()
                    ->where('danh_muc_id', $newDanhMucId)
                    ->where('thu_tu', '>=', $newThuTu)
                    ->update(['thu_tu' => DB::raw('thu_tu + 1')]);
            } elseif ($newThuTu !== $oldThuTu) {
                if ($newThuTu < $oldThuTu) {
                    BaiHoc::query()
                        ->where('danh_muc_id', $oldDanhMucId)
                        ->where('id', '!=', $baiHoc->id)
                        ->whereBetween('thu_tu', [$newThuTu, $oldThuTu - 1])
                        ->update(['thu_tu' => DB::raw('thu_tu + 1')]);
                } else {
                    BaiHoc::query()
                        ->where('danh_muc_id', $oldDanhMucId)
                        ->where('id', '!=', $baiHoc->id)
                        ->whereBetween('thu_tu', [$oldThuTu + 1, $newThuTu])
                        ->update(['thu_tu' => DB::raw('thu_tu - 1')]);
                }
            }

            $baiHoc->update([
                'danh_muc_id' => $newDanhMucId,
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'cap_do' => $request->cap_do,
                'thu_tu' => $newThuTu,
                // Mọi chỉnh sửa từ giáo viên đều đưa bài về hàng chờ admin duyệt lại.
                'trang_thai' => 0,
            ]);
        });

        $baiHoc->load('tuVungs:id,bai_hoc_id');

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật bài học thành công',
            'data' => [
                'id' => $baiHoc->id,
                'danh_muc_id' => $baiHoc->danh_muc_id,
                'tieu_de' => $baiHoc->tieu_de,
                'mo_ta' => $baiHoc->mo_ta,
                'cap_do' => $baiHoc->cap_do,
                'so_luong_tu' => $baiHoc->tuVungs->count(),
                'trang_thai' => $this->mapTrangThaiBaiHocToTeacherUi($baiHoc->trang_thai),
                'trang_thai_so' => (int) $baiHoc->trang_thai,
            ],
        ]);
    }

    public function destroyBaiHoc(DestroyTeacherBaiHocRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::find($id);
        if (!$baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học',
            ], 404);
        }

        $baiHoc->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa bài học thành công',
        ]);
    }

    private function iconFromMoTa(?string $moTa): string
    {
        if ($moTa === null || $moTa === '') {
            return 'fa-solid fa-folder';
        }

        $decoded = json_decode($moTa, true);
        if (is_array($decoded) && !empty($decoded['icon']) && is_string($decoded['icon'])) {
            return $decoded['icon'];
        }

        return 'fa-solid fa-folder';
    }

    /**
     * Giáo viên: 1 = hiển thị/đã duyệt (Hoạt động), 0 = nháp/chờ duyệt, 2 = từ chối (vẫn coi là nháp trên UI đơn giản).
     */
    private function mapTrangThaiBaiHocToTeacherUi(?int $trangThai): string
    {
        return (int) $trangThai === 1 ? 'hoat_dong' : 'nhap';
    }

    private function nextThuTuTrongDanhMuc(int $danhMucId): int
    {
        $max = BaiHoc::where('danh_muc_id', $danhMucId)->max('thu_tu');

        return $max === null ? 1 : (int) $max + 1;
    }

    private function resolveThuTuKhiThemBaiHoc(int $danhMucId, mixed $thuTuInput): int
    {
        if ($thuTuInput === null || $thuTuInput === '') {
            return $this->nextThuTuTrongDanhMuc($danhMucId);
        }

        return max(1, (int) $thuTuInput);
    }

    private function resolveThuTuKhiCapNhatBaiHoc(BaiHoc $baiHoc, int $newDanhMucId, mixed $thuTuInput): int
    {
        if ($thuTuInput === null || $thuTuInput === '') {
            if ((int) $baiHoc->danh_muc_id === $newDanhMucId) {
                return (int) $baiHoc->thu_tu;
            }

            return $this->nextThuTuTrongDanhMuc($newDanhMucId);
        }

        return max(1, (int) $thuTuInput);
    }
}
