<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BaiHocController extends Controller
{
    public function indexPublic(Request $request): JsonResponse
    {
        // Validate toàn bộ input một lần — tránh rò rỉ giá trị không hợp lệ vào query
        $input = $request->validate([
            'danh_muc_id' => ['sometimes', 'integer', 'exists:danh_muc_bai_hocs,id'],
            'tim_kiem'    => ['sometimes', 'string', 'max:100'],
            'cap_do'      => ['sometimes', Rule::in(['basic', 'intermediate', 'advanced'])],
        ]);

        // Đồng bộ với TeacherQuanLyBaiHocController: 0 = hoat_dong (hiển thị học viên), 1 = nhap
        $query = BaiHoc::query()
            ->with([
                'danhMuc' => function ($q): void {
                    $q->select('id', 'ten_danh_muc', 'slug_danh_muc');
                },
            ])
            ->where('trang_thai', 0)
            ->orderBy('thu_tu');

        $meta = null;

        if (isset($input['danh_muc_id'])) {
            $query->where('danh_muc_id', $input['danh_muc_id']);

            $danhMuc = DanhMucBaiHoc::query()->find($input['danh_muc_id']);
            if ($danhMuc !== null) {
                $meta = [
                    'danh_muc' => [
                        'id'           => $danhMuc->id,
                        'ten_danh_muc' => $danhMuc->ten_danh_muc,
                        'slug_danh_muc'=> $danhMuc->slug_danh_muc,
                    ],
                ];
            }
        }

        if (isset($input['tim_kiem'])) {
            // Escape ký tự wildcard của SQL LIKE trước khi ghép vào pattern.
            // PDO binding chặn SQL injection nhưng không escape '%' và '_'.
            $raw     = trim($input['tim_kiem']);
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $raw);
            $keyword = '%' . $escaped . '%';
            // Bọc trong closure để nhóm OR thành một khối AND duy nhất,
            // đảm bảo điều kiện trang_thai = 1 ở ngoài luôn được áp dụng.
            $query->where(function ($q) use ($keyword): void {
                $q->where('tieu_de', 'like', $keyword)
                  ->orWhereHas('danhMuc', function ($q2) use ($keyword): void {
                      $q2->where('ten_danh_muc', 'like', $keyword);
                  });
            });
        }

        if (isset($input['cap_do'])) {
            $query->where('cap_do', $input['cap_do']);
        }

        $perPage = 12;
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data'       => $paginated->items(),
            'pagination' => [
                'trang_hien_tai' => $paginated->currentPage(),
                'trang_cuoi'     => $paginated->lastPage(),
                'tong_so'        => $paginated->total(),
                'con_trang_tiep' => $paginated->hasMorePages(),
            ],
            'meta' => $meta,
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
            'nguoiTao' => function ($q): void {
                $q->select('id', 'ho_ten');
            },
            'tuVungs',
        ]);

        $tuVungs = $baiHoc->tuVungs->map(function ($tv) {
            return [
                'id' => $tv->id,
                'tu_chuan' => $tv->tu_chuan,
                'phien_am' => $tv->phien_am,
                'cap_do' => $tv->cap_do,
                'hinh_anh_url' => $this->resolvePublicMediaUrl($tv->hinh_anh_url),
                'am_thanh_mau_url' => $this->resolvePublicMediaUrl($tv->am_thanh_mau_url),
                'thu_tu' => $tv->thu_tu,
            ];
        });

        return response()->json([
            'data' => [
                'id' => $baiHoc->id,
                'tieu_de' => $baiHoc->tieu_de,
                'mo_ta' => $baiHoc->mo_ta,
                'cap_do' => $baiHoc->cap_do,
                'thu_tu' => $baiHoc->thu_tu,
                'nguoi_tao_id' => $baiHoc->nguoi_tao_id,
                'giao_vien' => $baiHoc->nguoiTao ? [
                    'id' => $baiHoc->nguoiTao->id,
                    'ho_ten' => $baiHoc->nguoiTao->ho_ten,
                ] : null,
                'danh_muc' => $baiHoc->danhMuc,
                'tu_vungs' => $tuVungs,
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
