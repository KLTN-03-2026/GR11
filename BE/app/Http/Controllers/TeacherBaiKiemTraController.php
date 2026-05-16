<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ResolvesTeacherMediaUrl;
use App\Models\BaiHoc;
use App\Models\BaiKiemTra;
use App\Models\CauHoiKiemTra;
use App\Models\LuaChonCauHoi;
use App\Models\PhienKiemTra;
use App\Models\TuVung;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherBaiKiemTraController extends Controller
{
    use ResolvesTeacherMediaUrl;

    public function baiHocHoatDong(Request $request): JsonResponse
    {
        $v = Validator::make($request->query(), [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'q' => 'nullable|string|max:200',
        ]);
        if ($v->fails()) {
            return response()->json(['status' => false, 'errors' => $v->errors()], 422);
        }

        $perPage = (int) ($v->validated()['per_page'] ?? 15);
        $q = isset($v->validated()['q']) ? trim((string) $v->validated()['q']) : '';

        $query = BaiHoc::query()
            ->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)
            ->with([
                'danhMuc:id,ten_danh_muc',
                'nguoiTao:id,ho_ten',
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where('tieu_de', 'like', '%'.$q.'%');
        }

        $paginator = $query->paginate($perPage)->through(function (BaiHoc $bh): array {
            return [
                'id' => $bh->id,
                'tieu_de' => $bh->tieu_de,
                'mo_ta' => $bh->mo_ta,
                'cap_do' => $bh->cap_do,
                'danh_muc' => $bh->danhMuc ? [
                    'id' => $bh->danhMuc->id,
                    'ten_danh_muc' => $bh->danhMuc->ten_danh_muc,
                ] : null,
                'nguoi_tao' => $bh->nguoiTao ? [
                    'id' => $bh->nguoiTao->id,
                    'ho_ten' => $bh->nguoiTao->ho_ten,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function tuVungChoQuiz(int $id): JsonResponse
    {
        $baiHoc = BaiHoc::query()
            ->where('id', $id)
            ->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)
            ->first();

        if (! $baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Bài học không tồn tại hoặc chưa được duyệt (hoạt động).',
            ], 404);
        }

        $rows = TuVung::query()
            ->where('bai_hoc_id', $id)
            ->orderBy('thu_tu')
            ->orderBy('id')
            ->get(['id', 'tu_chuan', 'hinh_anh_url', 'am_thanh_mau_url']);

        $data = $rows->map(fn (TuVung $tv): array => [
            'id' => $tv->id,
            'tu_chuan' => $tv->tu_chuan,
            'hinh_anh_url' => $this->resolveTeacherMediaUrl($tv->hinh_anh_url),
            'am_thanh_mau_url' => $this->resolveTeacherMediaUrl($tv->am_thanh_mau_url),
        ])->values()->all();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $items = BaiKiemTra::query()
            ->where('nguoi_tao_id', $request->user()->id)
            ->with(['baiHoc:id,tieu_de'])
            ->withCount('phienKiemTras')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request, int $baiHocId): JsonResponse
    {
        $baiHoc = BaiHoc::where('id', $baiHocId)->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG)->first();
        if (! $baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Bài học không tồn tại, chưa xuất bản, hoặc không hợp lệ.',
            ], 404);
        }

        $v = Validator::make($request->all(), [
            'tieu_de' => 'nullable|string|max:200',
            'mo_ta_huong_dan' => 'nullable|string',
            'thoi_gian_gioi_han_giay' => 'required|integer|min:30|max:86400',
            'diem_toi_thieu' => 'required|integer|min:0|max:1000',
            'trang_thai' => 'required|integer|in:'.BaiKiemTra::TRANG_THAI_NHAP.','.BaiKiemTra::TRANG_THAI_XUAT_BAN,
            'cau_hoi' => 'nullable|array',
            'cau_hoi.*.loai' => 'required_with:cau_hoi|string|in:mcq,phat_am',
            'cau_hoi.*.thu_tu' => 'required_with:cau_hoi|integer|min:1',
            'cau_hoi.*.noi_dung_cau' => 'nullable|string|max:5000',
            'cau_hoi.*.diem_toi_da' => 'required_with:cau_hoi|integer|min:1|max:100',
            'cau_hoi.*.tu_vung_id' => 'nullable|integer|exists:tu_vungs,id',
            'cau_hoi.*.lua_chon' => 'nullable|array',
            'cau_hoi.*.lua_chon.*.noi_dung' => 'required_with:cau_hoi.*.lua_chon|string|max:500',
            'cau_hoi.*.lua_chon.*.la_dung' => 'required_with:cau_hoi.*.lua_chon|boolean',
            'cau_hoi.*.lua_chon.*.thu_tu' => 'nullable|integer|min:1',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => false, 'errors' => $v->errors()], 422);
        }

        $data = $v->validated();
        if (! empty($data['cau_hoi'])) {
            $err = $this->validateCauHoiList($data['cau_hoi'], $baiHocId);
            if ($err !== null) {
                return $err;
            }
        }

        $quizId = null;
        DB::transaction(function () use ($request, $baiHocId, $data, &$quizId): void {
            $created = BaiKiemTra::create([
                'bai_hoc_id' => $baiHocId,
                'nguoi_tao_id' => $request->user()->id,
                'tieu_de' => $data['tieu_de'] ?? null,
                'mo_ta_huong_dan' => $data['mo_ta_huong_dan'] ?? null,
                'thoi_gian_gioi_han_giay' => $data['thoi_gian_gioi_han_giay'],
                'diem_toi_thieu' => $data['diem_toi_thieu'],
                'trang_thai' => $data['trang_thai'],
            ]);
            $quizId = $created->id;

            if (! empty($data['cau_hoi'])) {
                $this->syncCauHoi($created->id, $data['cau_hoi']);
            }
        });

        $quiz = BaiKiemTra::query()
            ->where('id', $quizId)
            ->with(['cauHois' => fn ($q) => $q->orderBy('thu_tu')->with(['luaChons', 'tuVung:id,tu_chuan,hinh_anh_url,am_thanh_mau_url,bai_hoc_id'])])
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Đã tạo bài kiểm tra.',
            'data' => $quiz,
        ], 201);
    }

    public function show(Request $request, int $baiKiemTraId): JsonResponse
    {
        $quiz = BaiKiemTra::query()
            ->where('id', $baiKiemTraId)
            ->where('nguoi_tao_id', $request->user()->id)
            ->withCount('phienKiemTras')
            ->with([
                'baiHoc:id,tieu_de',
                'cauHois' => function ($q): void {
                    $q->orderBy('thu_tu')->with(['luaChons' => fn ($q2) => $q2->orderBy('thu_tu'), 'tuVung:id,tu_chuan,hinh_anh_url,am_thanh_mau_url,bai_hoc_id']);
                },
            ])
            ->first();

        if (! $quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài kiểm tra hoặc bạn không có quyền.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $quiz,
        ]);
    }

    public function ketQuaHocVien(Request $request, int $baiKiemTraId): JsonResponse
    {
        $quiz = BaiKiemTra::query()
            ->where('id', $baiKiemTraId)
            ->where('nguoi_tao_id', $request->user()->id)
            ->first();

        if (! $quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài kiểm tra hoặc bạn không có quyền.',
            ], 404);
        }

        $rows = PhienKiemTra::query()
            ->where('bai_kiem_tra_id', $quiz->id)
            ->whereIn('trang_thai', [PhienKiemTra::TRANG_THAI_NOP, PhienKiemTra::TRANG_THAI_HET_GIO])
            ->with(['nguoiDung:id,ho_ten,email'])
            ->orderByDesc('thoi_gian_ket_thuc')
            ->orderByDesc('id')
            ->get();

        $data = $rows->map(function (PhienKiemTra $phien) use ($quiz): array {
            $batDau = $phien->thoi_gian_bat_dau;
            $ketThuc = $phien->thoi_gian_ket_thuc;

            return [
                'phien_kiem_tra_id' => $phien->id,
                'hoc_vien' => $phien->nguoiDung ? [
                    'id' => $phien->nguoiDung->id,
                    'ho_ten' => $phien->nguoiDung->ho_ten,
                    'email' => $phien->nguoiDung->email,
                ] : null,
                'tong_diem' => (int) ($phien->tong_diem ?? 0),
                'dat' => (int) ($phien->tong_diem ?? 0) >= (int) $quiz->diem_toi_thieu,
                'trang_thai' => (int) $phien->trang_thai,
                'thoi_gian_bat_dau' => $batDau ? $batDau->format('c') : null,
                'thoi_gian_ket_thuc' => $ketThuc ? $ketThuc->format('c') : null,
                'thoi_gian_lam_giay' => ($batDau && $ketThuc) ? $ketThuc->diffInSeconds($batDau) : null,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function destroy(Request $request, int $baiKiemTraId): JsonResponse
    {
        $quiz = BaiKiemTra::query()
            ->where('id', $baiKiemTraId)
            ->where('nguoi_tao_id', $request->user()->id)
            ->first();

        if (! $quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài kiểm tra hoặc bạn không có quyền.',
            ], 404);
        }

        if (PhienKiemTra::where('bai_kiem_tra_id', $quiz->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa: đã có học viên làm bài kiểm tra này.',
            ], 409);
        }

        DB::transaction(function () use ($quiz): void {
            $quiz->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa bài kiểm tra.',
        ]);
    }

    public function update(Request $request, int $baiKiemTraId): JsonResponse
    {
        $quiz = BaiKiemTra::query()
            ->where('id', $baiKiemTraId)
            ->where('nguoi_tao_id', $request->user()->id)
            ->first();

        if (! $quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài kiểm tra hoặc bạn không có quyền.',
            ], 404);
        }

        $baiHocId = (int) $quiz->bai_hoc_id;

        $metaOnly = filter_var($request->input('chi_cap_nhat_meta', false), FILTER_VALIDATE_BOOLEAN);

        if ($metaOnly) {
            $v = Validator::make($request->all(), [
                'tieu_de' => 'nullable|string|max:200',
                'mo_ta_huong_dan' => 'nullable|string',
                'thoi_gian_gioi_han_giay' => 'required|integer|min:30|max:86400',
                'diem_toi_thieu' => 'required|integer|min:0|max:1000',
                'trang_thai' => 'required|integer|in:'.BaiKiemTra::TRANG_THAI_NHAP.','.BaiKiemTra::TRANG_THAI_XUAT_BAN,
            ]);
            if ($v->fails()) {
                return response()->json(['status' => false, 'errors' => $v->errors()], 422);
            }

            $quiz->update($v->validated());
            $fresh = $quiz->fresh(['baiHoc:id,tieu_de']);
            $fresh->loadCount('phienKiemTras');

            return response()->json([
                'status' => true,
                'message' => 'Đã cập nhật cấu hình bài kiểm tra.',
                'data' => $fresh,
            ]);
        }

        $v = Validator::make($request->all(), [
            'tieu_de' => 'nullable|string|max:200',
            'mo_ta_huong_dan' => 'nullable|string',
            'thoi_gian_gioi_han_giay' => 'required|integer|min:30|max:86400',
            'diem_toi_thieu' => 'required|integer|min:0|max:1000',
            'trang_thai' => 'required|integer|in:'.BaiKiemTra::TRANG_THAI_NHAP.','.BaiKiemTra::TRANG_THAI_XUAT_BAN,
            'cau_hoi' => 'required|array|min:1',
            'cau_hoi.*.loai' => 'required|string|in:mcq,phat_am',
            'cau_hoi.*.thu_tu' => 'required|integer|min:1',
            'cau_hoi.*.noi_dung_cau' => 'nullable|string|max:5000',
            'cau_hoi.*.diem_toi_da' => 'required|integer|min:1|max:100',
            'cau_hoi.*.tu_vung_id' => 'nullable|integer|exists:tu_vungs,id',
            'cau_hoi.*.lua_chon' => 'nullable|array',
            'cau_hoi.*.lua_chon.*.noi_dung' => 'required_with:cau_hoi.*.lua_chon|string|max:500',
            'cau_hoi.*.lua_chon.*.la_dung' => 'required_with:cau_hoi.*.lua_chon|boolean',
            'cau_hoi.*.lua_chon.*.thu_tu' => 'nullable|integer|min:1',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => false, 'errors' => $v->errors()], 422);
        }

        $data = $v->validated();
        $err = $this->validateCauHoiList($data['cau_hoi'], $baiHocId);
        if ($err !== null) {
            return $err;
        }

        if (PhienKiemTra::where('bai_kiem_tra_id', $quiz->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Đã có học viên làm bài kiểm tra; không thể thay đổi danh sách câu hỏi. Dùng chi_cap_nhat_meta=true để sửa thời gian/điểm đạt.',
            ], 409);
        }

        DB::transaction(function () use ($quiz, $data): void {
            $quiz->update([
                'tieu_de' => $data['tieu_de'] ?? null,
                'mo_ta_huong_dan' => $data['mo_ta_huong_dan'] ?? null,
                'thoi_gian_gioi_han_giay' => $data['thoi_gian_gioi_han_giay'],
                'diem_toi_thieu' => $data['diem_toi_thieu'],
                'trang_thai' => $data['trang_thai'],
            ]);

            CauHoiKiemTra::where('bai_kiem_tra_id', $quiz->id)->delete();
            $this->syncCauHoi($quiz->id, $data['cau_hoi']);
        });

        $quiz = BaiKiemTra::query()
            ->where('id', $baiKiemTraId)
            ->with(['cauHois' => fn ($q) => $q->orderBy('thu_tu')->with(['luaChons', 'tuVung:id,tu_chuan,hinh_anh_url,am_thanh_mau_url,bai_hoc_id'])])
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu bài kiểm tra.',
            'data' => $quiz,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cauHoiList
     */
    private function validateCauHoiList(array $cauHoiList, int $baiHocId): ?JsonResponse
    {
        foreach ($cauHoiList as $idx => $ch) {
            if ($ch['loai'] === 'mcq') {
                if (empty($ch['lua_chon']) || count($ch['lua_chon']) < 2) {
                    return response()->json([
                        'status' => false,
                        'message' => "Câu hỏi MCQ tại vị trí {$idx} cần ít nhất 2 lựa chọn.",
                    ], 422);
                }
                $dung = collect($ch['lua_chon'])->where('la_dung', true)->count();
                if ($dung !== 1) {
                    return response()->json([
                        'status' => false,
                        'message' => "Mỗi câu MCQ phải có đúng một đáp án đúng (câu {$idx}).",
                    ], 422);
                }
            }
            if ($ch['loai'] === 'phat_am') {
                if (empty($ch['tu_vung_id'])) {
                    return response()->json([
                        'status' => false,
                        'message' => "Câu phát âm tại vị trí {$idx} cần tu_vung_id.",
                    ], 422);
                }
                $tv = TuVung::where('id', (int) $ch['tu_vung_id'])->where('bai_hoc_id', $baiHocId)->first();
                if (! $tv) {
                    return response()->json([
                        'status' => false,
                        'message' => "tu_vung_id không thuộc bài học này (câu {$idx}).",
                    ], 422);
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $cauHoiList
     */
    private function syncCauHoi(int $baiKiemTraId, array $cauHoiList): void
    {
        foreach ($cauHoiList as $ch) {
            $cau = CauHoiKiemTra::create([
                'bai_kiem_tra_id' => $baiKiemTraId,
                'tu_vung_id' => $ch['loai'] === 'phat_am' ? (int) $ch['tu_vung_id'] : null,
                'loai' => $ch['loai'],
                'thu_tu' => (int) $ch['thu_tu'],
                'noi_dung_cau' => $ch['noi_dung_cau'] ?? null,
                'diem_toi_da' => (int) $ch['diem_toi_da'],
            ]);

            if ($ch['loai'] === 'mcq' && ! empty($ch['lua_chon'])) {
                foreach ($ch['lua_chon'] as $lc) {
                    LuaChonCauHoi::create([
                        'cau_hoi_kiem_tra_id' => $cau->id,
                        'noi_dung' => $lc['noi_dung'],
                        'la_dung' => (bool) $lc['la_dung'],
                        'thu_tu' => (int) ($lc['thu_tu'] ?? 1),
                    ]);
                }
            }
        }
    }
}
