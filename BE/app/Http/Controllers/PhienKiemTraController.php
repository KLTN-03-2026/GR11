<?php

namespace App\Http\Controllers;

use App\Models\BaiHoc;
use App\Models\BaiKiemTra;
use App\Models\CauHoiKiemTra;
use App\Models\ChiTietPhienKiemTra;
use App\Models\LuaChonCauHoi;
use App\Models\PhienKiemTra;
use App\Models\QuanHeGvHv;
use App\Models\TienDoBaiHoc;
use App\Services\PhatAmScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Phiên làm bài kiểm tra (summative), tách khỏi {@see PhienLuyenTapController}.
 * Nộp bài ({@see nopBai}) chỉ cập nhật {@see TienDoBaiHoc} — không cộng streak / điểm tích lũy luyện tập.
 */
class PhienKiemTraController extends Controller
{
    public function __construct(
        private readonly PhatAmScoringService $phatAmScoring
    ) {}

    /**
     * Bài kiểm tra đã xuất bản gắn với học viên: bài học đã có tiến độ hoặc đã từng có phiên kiểm tra.
     */
    public function danhSachChoHocVien(Request $request): JsonResponse
    {
        $user = $request->user();

        $coGiaoVienKetNoi = QuanHeGvHv::query()
            ->where('hoc_vien_id', $user->id)
            ->where('trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
            ->exists();

        $baiHocCoTienDo = TienDoBaiHoc::query()
            ->where('hoc_vien_id', $user->id)
            ->pluck('bai_hoc_id');

        $baiKiemTraCoPhien = PhienKiemTra::query()
            ->where('nguoi_dung_id', $user->id)
            ->pluck('bai_kiem_tra_id');

        $rows = BaiKiemTra::query()
            ->where('trang_thai', BaiKiemTra::TRANG_THAI_XUAT_BAN)
            ->whereExists(function ($sub) use ($user): void {
                $sub->selectRaw('1')
                    ->from('quan_he_gv_hvs')
                    ->whereColumn('quan_he_gv_hvs.giao_vien_id', 'bai_kiem_tras.nguoi_tao_id')
                    ->where('quan_he_gv_hvs.hoc_vien_id', $user->id)
                    ->where('quan_he_gv_hvs.trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI);
            })
            ->where(function ($q) use ($baiHocCoTienDo, $baiKiemTraCoPhien): void {
                $q->whereIn('bai_hoc_id', $baiHocCoTienDo)
                    ->orWhereIn('id', $baiKiemTraCoPhien);
            })
            ->with(['baiHoc:id,tieu_de', 'nguoiTao:id,ho_ten,anh_dai_dien'])
            ->orderByDesc('updated_at')
            ->get();

        $tongDiemTheoQuiz = $rows->isEmpty()
            ? collect()
            : CauHoiKiemTra::query()
                ->whereIn('bai_kiem_tra_id', $rows->pluck('id'))
                ->selectRaw('bai_kiem_tra_id, COALESCE(SUM(diem_toi_da), 0) as tong')
                ->groupBy('bai_kiem_tra_id')
                ->pluck('tong', 'bai_kiem_tra_id');

        $data = $rows->map(function (BaiKiemTra $bk) use ($user, $tongDiemTheoQuiz) {
            $phienCuoi = PhienKiemTra::query()
                ->where('nguoi_dung_id', $user->id)
                ->where('bai_kiem_tra_id', $bk->id)
                ->orderByDesc('id')
                ->first();

            $td = TienDoBaiHoc::query()
                ->where('hoc_vien_id', $user->id)
                ->where('bai_hoc_id', $bk->bai_hoc_id)
                ->first();

            $gv = $bk->nguoiTao;

            return [
                'bai_hoc_id' => $bk->bai_hoc_id,
                'bai_hoc_tieu_de' => $bk->baiHoc?->tieu_de,
                'bai_kiem_tra_id' => $bk->id,
                'tieu_de' => $bk->tieu_de,
                'mo_ta_huong_dan' => $bk->mo_ta_huong_dan,
                'thoi_gian_gioi_han_giay' => $bk->thoi_gian_gioi_han_giay,
                'diem_toi_thieu' => $bk->diem_toi_thieu,
                'tong_diem_toi_da' => (int) ($tongDiemTheoQuiz[$bk->id] ?? 0),
                'diem_kiem_tra_tot_nhat' => $td?->diem_kiem_tra,
                'qua_kiem_tra' => $td?->qua_kiem_tra,
                'giao_vien' => $gv ? [
                    'id' => $gv->id,
                    'ho_ten' => $gv->ho_ten,
                    'anh_dai_dien' => $gv->anh_dai_dien,
                ] : null,
                'phien_gan_nhat' => $phienCuoi ? [
                    'id' => $phienCuoi->id,
                    'tong_diem' => $phienCuoi->tong_diem,
                    'trang_thai' => $phienCuoi->trang_thai,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'meta' => [
                'co_giao_vien_ket_noi' => $coGiaoVienKetNoi,
            ],
        ]);
    }

    /**
     * Lấy đề làm bài (ẩn đáp án đúng); yêu cầu đăng nhập.
     */
    public function deLamBai(Request $request, int $baiKiemTraId): JsonResponse
    {
        $user = $request->user();

        $quiz = BaiKiemTra::query()
            ->where('id', $baiKiemTraId)
            ->where('trang_thai', BaiKiemTra::TRANG_THAI_XUAT_BAN)
            ->with([
                'baiHoc:id,tieu_de,trang_thai',
                'cauHois' => function ($q): void {
                    $q->orderBy('thu_tu')->with(['luaChons' => fn ($q2) => $q2->orderBy('thu_tu'), 'tuVung:id,tu_chuan,hinh_anh_url,am_thanh_mau_url,bai_hoc_id']);
                },
            ])
            ->first();

        if (! $quiz) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy bài kiểm tra xuất bản.'], 404);
        }

        $baiHoc = $quiz->baiHoc;
        if (! $baiHoc || (int) $baiHoc->trang_thai !== BaiHoc::TRANG_THAI_HOAT_DONG) {
            return response()->json(['status' => false, 'message' => 'Bài học không tồn tại hoặc chưa hiển thị.'], 404);
        }

        if (! $this->coQuanHeGvHvActive((int) $user->id, (int) $quiz->nguoi_tao_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa kết nối với giáo viên của đề này.',
            ], 403);
        }

        $cauHoiPayload = $quiz->cauHois->map(function (CauHoiKiemTra $cau) {
            $payload = [
                'id' => $cau->id,
                'loai' => $cau->loai,
                'thu_tu' => $cau->thu_tu,
                'noi_dung_cau' => $cau->noi_dung_cau,
                'diem_toi_da' => $cau->diem_toi_da,
            ];

            if ($cau->loai === 'mcq') {
                $payload['lua_chon'] = $cau->luaChons->map(fn (LuaChonCauHoi $l) => [
                    'id' => $l->id,
                    'noi_dung' => $l->noi_dung,
                    'thu_tu' => $l->thu_tu,
                ])->values();
            }

            if ($cau->loai === 'phat_am' && $cau->tuVung) {
                $payload['tu_vung'] = [
                    'id' => $cau->tuVung->id,
                    'tu_chuan' => $cau->tuVung->tu_chuan,
                    'hinh_anh_url' => $cau->tuVung->hinh_anh_url,
                    'am_thanh_mau_url' => $cau->tuVung->am_thanh_mau_url,
                ];
            }

            return $payload;
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'bai_hoc' => [
                    'id' => $baiHoc->id,
                    'tieu_de' => $baiHoc->tieu_de,
                ],
                'bai_kiem_tra' => [
                    'id' => $quiz->id,
                    'tieu_de' => $quiz->tieu_de,
                    'mo_ta_huong_dan' => $quiz->mo_ta_huong_dan,
                    'thoi_gian_gioi_han_giay' => $quiz->thoi_gian_gioi_han_giay,
                    'diem_toi_thieu' => $quiz->diem_toi_thieu,
                ],
                'cau_hoi' => $cauHoiPayload,
            ],
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'bai_kiem_tra_id' => 'required|integer|exists:bai_kiem_tras,id',
        ]);

        $user = $request->user();
        $baiKiemTraId = (int) $request->input('bai_kiem_tra_id');

        $quiz = BaiKiemTra::query()
            ->with('baiHoc:id,tieu_de,trang_thai')
            ->where('id', $baiKiemTraId)
            ->where('trang_thai', BaiKiemTra::TRANG_THAI_XUAT_BAN)
            ->first();

        if (! $quiz) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy bài kiểm tra xuất bản.'], 404);
        }

        $baiHoc = $quiz->baiHoc;
        if (! $baiHoc || (int) $baiHoc->trang_thai !== BaiHoc::TRANG_THAI_HOAT_DONG) {
            return response()->json(['status' => false, 'message' => 'Bài học không tồn tại hoặc chưa hiển thị.'], 404);
        }

        if (! $this->coQuanHeGvHvActive((int) $user->id, (int) $quiz->nguoi_tao_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa kết nối với giáo viên của đề này.',
            ], 403);
        }

        $active = PhienKiemTra::where('nguoi_dung_id', $user->id)
            ->where('bai_kiem_tra_id', $quiz->id)
            ->where('trang_thai', PhienKiemTra::TRANG_THAI_DANG_LAM)
            ->first();

        if ($active) {
            return response()->json([
                'status' => true,
                'message' => 'Tiếp tục phiên đang làm.',
                'data' => ['phien_kiem_tra_id' => $active->id],
            ]);
        }

        $phien = PhienKiemTra::create([
            'nguoi_dung_id' => $user->id,
            'bai_kiem_tra_id' => $quiz->id,
            'thoi_gian_bat_dau' => now(),
            'trang_thai' => PhienKiemTra::TRANG_THAI_DANG_LAM,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã bắt đầu phiên kiểm tra.',
            'data' => ['phien_kiem_tra_id' => $phien->id],
        ]);
    }

    public function luuCau(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phien_kiem_tra_id' => 'required|integer|exists:phien_kiem_tras,id',
            'cau_hoi_kiem_tra_id' => 'required|integer|exists:cau_hoi_kiem_tras,id',
            'lua_chon_id' => 'required|integer|exists:lua_chon_cau_hois,id',
        ]);

        $user = $request->user();
        $phien = PhienKiemTra::findOrFail($data['phien_kiem_tra_id']);
        if ((int) $phien->nguoi_dung_id !== (int) $user->id) {
            return response()->json(['status' => false, 'message' => 'Không có quyền.'], 403);
        }
        if ($phien->trang_thai !== PhienKiemTra::TRANG_THAI_DANG_LAM) {
            return response()->json(['status' => false, 'message' => 'Phiên đã kết thúc.'], 422);
        }

        $cau = CauHoiKiemTra::findOrFail($data['cau_hoi_kiem_tra_id']);
        if ((int) $cau->bai_kiem_tra_id !== (int) $phien->bai_kiem_tra_id) {
            return response()->json(['status' => false, 'message' => 'Câu hỏi không thuộc phiên này.'], 422);
        }
        if ($cau->loai !== 'mcq') {
            return response()->json(['status' => false, 'message' => 'Câu này không phải trắc nghiệm.'], 422);
        }

        $lua = LuaChonCauHoi::findOrFail($data['lua_chon_id']);
        if ((int) $lua->cau_hoi_kiem_tra_id !== (int) $cau->id) {
            return response()->json(['status' => false, 'message' => 'Lựa chọn không thuộc câu hỏi.'], 422);
        }

        $diem = $lua->la_dung ? (int) $cau->diem_toi_da : 0;

        ChiTietPhienKiemTra::updateOrCreate(
            [
                'phien_kiem_tra_id' => $phien->id,
                'cau_hoi_kiem_tra_id' => $cau->id,
            ],
            [
                'lua_chon_id' => $lua->id,
                'diem_dat' => $diem,
            ]
        );

        $dapAnDungId = (int) (LuaChonCauHoi::query()
            ->where('cau_hoi_kiem_tra_id', $cau->id)
            ->where('la_dung', true)
            ->value('id') ?? 0);

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu.',
            'data' => [
                'diem_cau' => $diem,
                'chon_dung' => (bool) $lua->la_dung,
                'lua_chon_id' => (int) $lua->id,
                'dap_an_dung_id' => $dapAnDungId,
                'diem_toi_da' => (int) $cau->diem_toi_da,
            ],
        ]);
    }

    public function chamPhatAm(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phien_kiem_tra_id' => 'required|integer|exists:phien_kiem_tras,id',
            'cau_hoi_kiem_tra_id' => 'required|integer|exists:cau_hoi_kiem_tras,id',
            'audio' => ['required', 'file', 'mimes:webm,ogg,wav,mp3', 'max:10240'],
        ]);

        $user = $request->user();
        $phien = PhienKiemTra::findOrFail($data['phien_kiem_tra_id']);
        if ((int) $phien->nguoi_dung_id !== (int) $user->id) {
            return response()->json(['status' => false, 'message' => 'Không có quyền.'], 403);
        }
        if ($phien->trang_thai !== PhienKiemTra::TRANG_THAI_DANG_LAM) {
            return response()->json(['status' => false, 'message' => 'Phiên đã kết thúc.'], 422);
        }

        $cau = CauHoiKiemTra::with('tuVung')->findOrFail($data['cau_hoi_kiem_tra_id']);
        if ((int) $cau->bai_kiem_tra_id !== (int) $phien->bai_kiem_tra_id || $cau->loai !== 'phat_am') {
            return response()->json(['status' => false, 'message' => 'Câu hỏi không hợp lệ.'], 422);
        }
        if (! $cau->tu_vung_id || ! $cau->tuVung) {
            return response()->json(['status' => false, 'message' => 'Thiếu từ vựng chuẩn cho câu phát âm.'], 422);
        }

        $audioFile = $request->file('audio');
        $storagePath = "quiz_recordings/{$phien->id}/{$cau->id}_".time().'.'.$audioFile->getClientOriginalExtension();
        Storage::disk('local')->put($storagePath, file_get_contents($audioFile->getRealPath()));

        try {
            $ai = $this->phatAmScoring->analyze($audioFile, $cau->tuVung->tu_chuan);
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();
            if ($code === 'AI_SERVICE_UNAVAILABLE') {
                return response()->json(['status' => false, 'message' => 'AI service không phản hồi.'], 503);
            }

            return response()->json(['status' => false, 'message' => 'AI service trả về lỗi.'], 502);
        }

        $rawDiem = (float) ($ai['diem'] ?? 0);
        $diemToiDa = max(1, (int) $cau->diem_toi_da);
        $diemDat = (int) min($diemToiDa, round($rawDiem * $diemToiDa / 100));

        $phanHoi = [
            'van_ban_nhan_dien' => $ai['van_ban_nhan_dien'] ?? '',
            'diem_tin_cay' => $ai['diem_tin_cay'] ?? null,
            'loi_am_dau' => $ai['loi_am_dau'] ?? false,
            'loi_van' => $ai['loi_van'] ?? false,
            'loi_thanh_dieu' => $ai['loi_thanh_dieu'] ?? false,
            'chi_tiet' => $ai['chi_tiet'] ?? null,
        ];

        ChiTietPhienKiemTra::updateOrCreate(
            [
                'phien_kiem_tra_id' => $phien->id,
                'cau_hoi_kiem_tra_id' => $cau->id,
            ],
            [
                'file_ghi_am_url' => $storagePath,
                'diem_dat' => $diemDat,
                'phan_hoi' => $phanHoi,
            ]
        );

        return response()->json([
            'status' => true,
            'data' => [
                'diem' => $diemDat,
                'diem_toi_da' => $diemToiDa,
                'van_ban_nhan_dien' => $phanHoi['van_ban_nhan_dien'],
                'tu_chuan' => $cau->tuVung->tu_chuan,
                'chi_tiet' => $phanHoi['chi_tiet'],
            ],
        ], 201);
    }

    /**
     * Nộp bài: chấm tổng, cập nhật tiến độ kiểm tra. Không cộng streak / điểm tích lũy luyện tập.
     */
    public function nopBai(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phien_kiem_tra_id' => 'required|integer|exists:phien_kiem_tras,id',
        ]);

        $user = $request->user();
        $phien = PhienKiemTra::with('baiKiemTra')->findOrFail($data['phien_kiem_tra_id']);
        if ((int) $phien->nguoi_dung_id !== (int) $user->id) {
            return response()->json(['status' => false, 'message' => 'Không có quyền.'], 403);
        }
        if ($phien->trang_thai !== PhienKiemTra::TRANG_THAI_DANG_LAM) {
            return response()->json(['status' => false, 'message' => 'Phiên đã được nộp trước đó.'], 422);
        }

        $quiz = $phien->baiKiemTra;
        $limitSec = max(1, (int) $quiz->thoi_gian_gioi_han_giay);
        $batDau = $phien->thoi_gian_bat_dau ?? $phien->created_at;
        $hetHan = $batDau->copy()->addSeconds($limitSec);
        $quaHan = now()->greaterThan($hetHan);

        DB::transaction(function () use ($phien, $quiz, $quaHan): void {
            $cauIds = CauHoiKiemTra::where('bai_kiem_tra_id', $quiz->id)->pluck('id');
            $tong = 0;
            foreach ($cauIds as $cid) {
                $row = ChiTietPhienKiemTra::where('phien_kiem_tra_id', $phien->id)
                    ->where('cau_hoi_kiem_tra_id', $cid)
                    ->first();
                $tong += (int) ($row->diem_dat ?? 0);
            }

            $phien->tong_diem = $tong;
            $phien->thoi_gian_ket_thuc = now();
            $phien->trang_thai = $quaHan ? PhienKiemTra::TRANG_THAI_HET_GIO : PhienKiemTra::TRANG_THAI_NOP;
            $phien->save();

            $diemToiThieu = (int) $quiz->diem_toi_thieu;
            $qua = $tong >= $diemToiThieu;

            $td = TienDoBaiHoc::firstOrCreate(
                [
                    'hoc_vien_id' => $phien->nguoi_dung_id,
                    'bai_hoc_id' => $quiz->bai_hoc_id,
                ],
                [
                    'so_tu_da_hoc' => 0,
                    'phan_tram_hoan_thanh' => 0,
                    'trang_thai' => 0,
                    'diem_trung_binh' => 0,
                ]
            );

            $best = max((float) ($td->diem_kiem_tra ?? 0), (float) $tong);
            $td->diem_kiem_tra = round($best, 2);
            $td->qua_kiem_tra = ((int) ($td->qua_kiem_tra ?? 0) === 1 || $qua) ? 1 : 0;
            $td->thoi_gian_kiem_tra_cuoi = now();
            $td->save();
        });

        $phien->refresh();

        $tongDiemToiDa = (int) CauHoiKiemTra::query()
            ->where('bai_kiem_tra_id', $quiz->id)
            ->sum('diem_toi_da');

        return response()->json([
            'status' => true,
            'message' => 'Đã nộp bài.',
            'data' => [
                'phien_kiem_tra_id' => $phien->id,
                'tong_diem' => $phien->tong_diem,
                'tong_diem_toi_da' => $tongDiemToiDa,
                'qua' => $phien->tong_diem >= $quiz->diem_toi_thieu,
                'diem_toi_thieu' => $quiz->diem_toi_thieu,
                'trang_thai' => $phien->trang_thai,
            ],
        ]);
    }

    private function coQuanHeGvHvActive(int $hocVienId, int $giaoVienId): bool
    {
        return QuanHeGvHv::query()
            ->where('giao_vien_id', $giaoVienId)
            ->where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', QuanHeGvHv::TRANG_THAI_DANG_KET_NOI)
            ->exists();
    }
}
