<?php

namespace App\Http\Controllers;

use App\Events\AdminDuyetBaiHoc;
use App\Http\Requests\ChangeStatusKiemDuyetRequest;
use App\Http\Requests\DestroyDanhMucBaiHocRequest;
use App\Http\Requests\GetBaiHocTheoDanhMucRequest;
use App\Http\Requests\GetTuVungTheoBaiHocRequest;
use App\Http\Requests\StoreDanhMucBaiHocRequest;
use App\Http\Requests\UpdateDanhMucBaiHocRequest;
use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KiemDuyetBaiHocConTroller extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($this->isKiemDuyetBaiHocRoute($request)) {
            $data = BaiHoc::with(['danhMuc:id,ten_danh_muc', 'tuVungs:id,bai_hoc_id', 'nguoiTao:id,ho_ten'])
                ->orderByDesc('id')
                ->get()
                ->map(function (BaiHoc $baiHoc) {
                    return [
                        'id' => $baiHoc->id,
                        'danh_muc_id' => $baiHoc->danh_muc_id,
                        'tieu_de' => $baiHoc->tieu_de,
                        'cap_do' => $baiHoc->cap_do,
                        'so_luong_tu' => $baiHoc->tuVungs->count(),
                        'nguoi_tao_ten' => $baiHoc->nguoiTao ? $baiHoc->nguoiTao->ho_ten : 'Giáo viên',
                        'ngay_tao' => optional($baiHoc->created_at)->format('d/m/Y'),
                        'trang_thai' => $this->mapTrangThaiBaiHocToText($baiHoc->trang_thai),
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        }

        $data = DanhMucBaiHoc::orderBy('thu_tu')->orderByDesc('id')->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function store(StoreDanhMucBaiHocRequest $request): JsonResponse
    {
        $danhMuc = DanhMucBaiHoc::create([
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug_danh_muc' => $request->slug_danh_muc,
            'mo_ta' => $request->mo_ta,
            'thu_tu' => $request->thu_tu ?? 1,
            'trang_thai' => (int) $request->input('trang_thai', 1),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm danh mục thành công',
            'data' => $danhMuc,
        ]);
    }

    public function update(UpdateDanhMucBaiHocRequest $request, int $id): JsonResponse
    {
        $danhMuc = DanhMucBaiHoc::find($id);
        if (! $danhMuc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy danh mục',
            ], 404);
        }

        $danhMuc->update([
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug_danh_muc' => $request->slug_danh_muc,
            'mo_ta' => $request->mo_ta,
            'thu_tu' => $request->input('thu_tu', $danhMuc->thu_tu),
            'trang_thai' => (int) $request->input('trang_thai', $danhMuc->trang_thai),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật danh mục thành công',
            'data' => $danhMuc->fresh(),
        ]);
    }

    public function destroy(DestroyDanhMucBaiHocRequest $request, int $id): JsonResponse
    {
        $danhMuc = DanhMucBaiHoc::find($id);
        if (! $danhMuc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy danh mục',
            ], 404);
        }

        $danhMuc->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa danh mục thành công',
        ]);
    }

    public function changeStatus(ChangeStatusKiemDuyetRequest $request, int $id): JsonResponse
    {
        if ($this->isKiemDuyetBaiHocRoute($request)) {
            $baiHoc = BaiHoc::find($id);
            if (! $baiHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy bài học',
                ], 404);
            }

            $trangThaiMoi = $this->resolveTrangThaiBaiHoc($request->input('trang_thai'));
            if ($trangThaiMoi === null) {
                $trangThaiMoi = $baiHoc->trang_thai == BaiHoc::TRANG_THAI_HOAT_DONG
                    ? BaiHoc::TRANG_THAI_CHO_DUYET
                    : BaiHoc::TRANG_THAI_HOAT_DONG;
            }

            $baiHoc->trang_thai = $trangThaiMoi;
            $baiHoc->save();

            // Broadcast real-time tới giáo viên sở hữu bài học
            if ($baiHoc->nguoi_tao_id) {
                $trangThaiLabel = $this->mapTrangThaiBaiHocToText($baiHoc->trang_thai);
                broadcast(new AdminDuyetBaiHoc($baiHoc, (int) $baiHoc->nguoi_tao_id, $trangThaiLabel));
            }

            return response()->json([
                'status' => true,
                'message' => 'Đã cập nhật trạng thái bài học thành công',
                'data' => [
                    'id' => $baiHoc->id,
                    'trang_thai' => $this->mapTrangThaiBaiHocToText($baiHoc->trang_thai),
                ],
            ]);
        }

        $danhMuc = DanhMucBaiHoc::find($id);
        if (! $danhMuc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy danh mục',
            ], 404);
        }

        $danhMuc->trang_thai = (int) ! $danhMuc->trang_thai;
        $danhMuc->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật trạng thái danh mục thành công',
            'data' => $danhMuc,
        ]);
    }

    public function getBaiHoc(GetBaiHocTheoDanhMucRequest $request, int $id): JsonResponse
    {
        $danhMuc = DanhMucBaiHoc::find($id);
        if (! $danhMuc) {
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
                    'cap_do' => $baiHoc->cap_do,
                    'so_luong_tu' => $baiHoc->tuVungs->count(),
                    'trang_thai' => $this->mapTrangThaiBaiHocToText($baiHoc->trang_thai),
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function getTuVung(GetTuVungTheoBaiHocRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::find($id);
        if (! $baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học',
            ], 404);
        }

        $data = $baiHoc->tuVungs()
            ->orderBy('thu_tu')
            ->get([
                'id',
                'bai_hoc_id',
                'tu_chuan',
                'phien_am',
                'cap_do',
                'hinh_anh_url',
                'am_thanh_mau_url',
                'thu_tu',
            ]);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    private function isKiemDuyetBaiHocRoute(Request $request): bool
    {
        return $request->is('api/admin/kiem-duyet-bai-hoc*');
    }

    private function mapTrangThaiBaiHocToText(?int $trangThai): string
    {
        if ((int) $trangThai === BaiHoc::TRANG_THAI_HOAT_DONG) {
            return 'Đã duyệt';
        }
        if ((int) $trangThai === BaiHoc::TRANG_THAI_TU_CHOI) {
            return 'Từ chối';
        }
        if ((int) $trangThai === BaiHoc::TRANG_THAI_CHO_DUYET) {
            return 'Chờ duyệt';
        }

        return 'Chờ duyệt';
    }

    private function resolveTrangThaiBaiHoc($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $value = mb_strtolower(trim((string) $value));
        if ($value === 'đã duyệt' || $value === 'da duyet') {
            return BaiHoc::TRANG_THAI_HOAT_DONG;
        }
        if ($value === 'từ chối' || $value === 'tu choi') {
            return BaiHoc::TRANG_THAI_TU_CHOI;
        }
        if ($value === 'chờ duyệt' || $value === 'cho duyet') {
            return BaiHoc::TRANG_THAI_CHO_DUYET;
        }

        return null;
    }
}
