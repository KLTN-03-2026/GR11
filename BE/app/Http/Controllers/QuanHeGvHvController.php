<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuiGoiYQuanHeGvHvRequest;
use App\Models\BaiHoc;
use App\Models\ChiTietLuyenTap;
use App\Models\GoiYLuyenTap;
use App\Models\NguoiDung;
use App\Models\PhienLuyenTap;
use App\Models\QuanHeGvHv;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanHeGvHvController extends Controller
{
    public function dashboardTongQuan(Request $request): JsonResponse
    {
        $giaoVien = $request->user();
        $hocVienIds = QuanHeGvHv::query()
            ->where('giao_vien_id', $giaoVien->id)
            ->pluck('hoc_vien_id');

        $tongHocVien = (int) $hocVienIds->count();

        $queryPhien = PhienLuyenTap::query()->whereIn('nguoi_dung_id', $hocVienIds);
        $tongPhien = (int) $queryPhien->count();
        $tongPhienTuan = (int) (clone $queryPhien)
            ->where(function ($q): void {
                $q->where('thoi_gian_bat_dau', '>=', now()->subDays(7))
                    ->orWhere('thoi_gian_ket_thuc', '>=', now()->subDays(7))
                    ->orWhere('ngay_tao', '>=', now()->subDays(7));
            })
            ->count();

        $tongBaiHocDangGiao = (int) BaiHoc::query()
            ->where('nguoi_tao_id', $giaoVien->id)
            ->where('trang_thai', 1)
            ->count();

        $hocVienCanChuY = (int) $this->demHocVienCanChuY($hocVienIds);
        $diemTrungBinh = (int) round((float) ((clone $queryPhien)->whereNotNull('tong_diem')->avg('tong_diem') ?? 0));
        $luotNopBai = $tongPhien;

        [$loiAmDau, $loiVan, $loiThanhDieu] = $this->thongKeLoiPhatAm($hocVienIds);
        $loiPhoBien = $this->xacDinhLoiPhoBien($loiAmDau, $loiVan, $loiThanhDieu);
        $tiLeChuyenCan = $tongHocVien > 0 ? (int) round(($hocVienIds->filter(function (int $hocVienId): bool {
            return PhienLuyenTap::query()
                ->where('nguoi_dung_id', $hocVienId)
                ->where(function ($q): void {
                    $q->where('thoi_gian_bat_dau', '>=', now()->subDays(7))
                        ->orWhere('thoi_gian_ket_thuc', '>=', now()->subDays(7))
                        ->orWhere('ngay_tao', '>=', now()->subDays(7));
                })
                ->exists();
        })->count() / $tongHocVien) * 100) : 0;

        $tuanNay = (int) (clone $queryPhien)
            ->where(function ($q): void {
                $q->where('thoi_gian_bat_dau', '>=', now()->subDays(7))
                    ->orWhere('thoi_gian_ket_thuc', '>=', now()->subDays(7))
                    ->orWhere('ngay_tao', '>=', now()->subDays(7));
            })
            ->count();
        $tuanTruoc = (int) (clone $queryPhien)
            ->where(function ($q): void {
                $q->whereBetween('thoi_gian_bat_dau', [now()->subDays(14), now()->subDays(7)])
                    ->orWhereBetween('thoi_gian_ket_thuc', [now()->subDays(14), now()->subDays(7)])
                    ->orWhereBetween('ngay_tao', [now()->subDays(14), now()->subDays(7)]);
            })
            ->count();
        $tiLeCaiThien = $tuanTruoc > 0
            ? (int) round((($tuanNay - $tuanTruoc) / $tuanTruoc) * 100)
            : ($tuanNay > 0 ? 100 : 0);

        return response()->json([
            'status' => true,
            'data' => [
                'the_tom_tat' => [
                    'hoc_sinh_tham_gia' => $tongHocVien,
                    'bai_hoc_dang_giao' => $tongBaiHocDangGiao,
                    'luot_luyen_tap_tuan' => $tongPhienTuan,
                    'hoc_sinh_can_chu_y' => $hocVienCanChuY,
                ],
                'thong_ke_lop_hoc' => [
                    'bai_dang_giao' => $tongBaiHocDangGiao,
                    'luot_nop_bai' => $luotNopBai,
                    'diem_trung_binh' => $diemTrungBinh,
                    'loi_pho_thong' => $loiPhoBien,
                    'ti_le_cai_thien' => $tiLeCaiThien,
                    'ti_le_chuyen_can' => $tiLeChuyenCan,
                ],
                'du_lieu_loi_phat_am' => [
                    'labels' => ['Sai thanh điệu', 'Sai âm đầu', 'Sai vần'],
                    'data' => [$loiThanhDieu, $loiAmDau, $loiVan],
                ],
                'du_lieu_bieu_do' => [
                    'day' => $this->duLieuPhienTheoNgay($hocVienIds),
                    'week' => $this->duLieuPhienTheoTuan($hocVienIds),
                    'month' => $this->duLieuPhienTheoThang($hocVienIds),
                    'year' => $this->duLieuPhienTheoNam($hocVienIds),
                ],
                'danh_sach_hoat_dong' => $this->layHoatDongGanDay($hocVienIds),
            ],
        ]);
    }

    public function danhSachHocVien(Request $request): JsonResponse
    {
        $giaoVien = $request->user();
        $timKiem = trim((string) $request->query('tim_kiem', ''));

        $hocVienIds = QuanHeGvHv::query()
            ->where('giao_vien_id', $giaoVien->id)
            ->pluck('hoc_vien_id');

        if ($hocVienIds->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => [],
            ]);
        }

        $query = NguoiDung::query()
            ->whereIn('id', $hocVienIds)
            ->where('vai_tro_id', NguoiDung::ROLE_USER);

        if ($timKiem !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $timKiem) . '%';
            $query->where(function ($q) use ($like): void {
                $q->where('ho_ten', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $hocViens = $query->orderBy('ho_ten')->get();

        $data = $hocViens->map(fn (NguoiDung $hv) => $this->tomTatHocVien($hv));

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function chiTietHocVien(Request $request, int $id): JsonResponse
    {
        $giaoVien = $request->user();

        if (!$this->laHocVienThuocGiaoVien($giaoVien->id, $id)) {
            return response()->json([
                'status' => false,
                'message' => 'Học viên không thuộc danh sách phụ trách của bạn.',
            ], 403);
        }

        $hv = NguoiDung::query()
            ->where('id', $id)
            ->where('vai_tro_id', NguoiDung::ROLE_USER)
            ->first();

        if (!$hv) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy học viên.',
            ], 404);
        }

        $tomTat = $this->tomTatHocVien($hv);
        $tomTat['commonMistakes'] = $this->layLoiThuongGap($id);
        $tomTat['history'] = $this->layLichSuLuyenTap($id);
        $tomTat['totalTime'] = $this->formatTongThoiGianLuyen($id);

        return response()->json([
            'status' => true,
            'data' => $tomTat,
        ]);
    }

    public function danhSachBaiHocGoiY(Request $request): JsonResponse
    {
        $baiHocs = BaiHoc::query()
            ->with(['danhMuc:id,ten_danh_muc'])
            ->orderBy('danh_muc_id')
            ->orderBy('thu_tu')
            ->orderByDesc('id')
            ->get(['id', 'danh_muc_id', 'tieu_de']);

        $nhom = $baiHocs->groupBy(fn (BaiHoc $b) => $b->danhMuc?->ten_danh_muc ?? 'Khác');

        $data = $nhom->map(function ($items, $tenDanhMuc) {
            return [
                'ten_danh_muc' => $tenDanhMuc,
                'bai_hoc' => $items->map(fn (BaiHoc $b) => [
                    'id' => $b->id,
                    'tieu_de' => $b->tieu_de,
                ])->values(),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function guiGoiY(GuiGoiYQuanHeGvHvRequest $request): JsonResponse
    {
        $giaoVien = $request->user();

        if (!$this->laHocVienThuocGiaoVien($giaoVien->id, (int) $request->hoc_vien_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không thể gửi gợi ý cho học viên này.',
            ], 403);
        }

        $baiHoc = BaiHoc::query()->find($request->bai_hoc_id);
        if (!$baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học.',
            ], 404);
        }

        $payload = [
            'type' => 'goi_y_bai_hoc',
            'bai_hoc_id' => (int) $baiHoc->id,
            'tieu_de' => $baiHoc->tieu_de,
            'uu_tien' => $request->uu_tien,
            'loi_nhan' => $request->loi_nhan,
            'ngay_gui' => now()->toIso8601String(),
        ];

        GoiYLuyenTap::query()->create([
            'giao_vien_id' => $giaoVien->id,
            'hoc_vien_id' => (int) $request->hoc_vien_id,
            'noi_dung' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'da_doc' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã gửi gợi ý luyện tập cho học viên.',
        ]);
    }

    private function laHocVienThuocGiaoVien(int $giaoVienId, int $hocVienId): bool
    {
        return QuanHeGvHv::query()
            ->where('giao_vien_id', $giaoVienId)
            ->where('hoc_vien_id', $hocVienId)
            ->exists();
    }

    /**
     * @return array<string, mixed>
     */
    private function tomTatHocVien(NguoiDung $hv): array
    {
        $hvId = $hv->id;

        $sessions = PhienLuyenTap::query()->where('nguoi_dung_id', $hvId)->count();

        $avg = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $hvId)
            ->whereNotNull('tong_diem')
            ->avg('tong_diem');

        $score = $avg !== null ? (int) round((float) $avg) : 0;

        $phienIds = PhienLuyenTap::query()->where('nguoi_dung_id', $hvId)->pluck('id');
        $errors = $this->taoBadgeLoiTuChiTiet($phienIds);

        $last = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $hvId)
            ->orderByDesc('id')
            ->first();

        [$lastLabel, $lastTime, $lastColor] = $this->formatHoatDongGanNhat($last);

        return [
            'id' => $hv->id,
            'name' => $hv->ho_ten,
            'email' => $hv->email,
            'phone' => $hv->sdt ?? '',
            'avatar' => $this->resolveAvatarUrl($hv->anh_dai_dien),
            'score' => $score,
            'sessions' => $sessions,
            'errors' => $errors,
            'lastActiveLabel' => $lastLabel,
            'lastActiveTime' => $lastTime,
            'lastActiveColor' => $lastColor,
        ];
    }

    /**
     * @param \Illuminate\Support\Collection<int, int> $phienIds
     * @return list<array{text: string, class: string}>
     */
    private function taoBadgeLoiTuChiTiet($phienIds): array
    {
        if ($phienIds->isEmpty()) {
            return [];
        }

        $am = (int) ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->where('loi_am_dau', 1)->count();
        $van = (int) ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->where('loi_van', 1)->count();
        $thanh = (int) ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->where('loi_thanh_dieu', 1)->count();

        $badges = [];
        if ($thanh > 0) {
            $badges[] = ['text' => 'Lỗi thanh điệu', 'class' => 'bg-warning text-dark'];
        }
        if ($am > 0) {
            $badges[] = ['text' => 'Lỗi âm đầu', 'class' => 'bg-danger'];
        }
        if ($van > 0) {
            $badges[] = ['text' => 'Lỗi vần', 'class' => 'bg-danger'];
        }

        $coChiTiet = $phienIds->isNotEmpty()
            && ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->exists();

        if ($badges === [] && $coChiTiet) {
            $badges[] = ['text' => 'Ổn định', 'class' => 'bg-secondary'];
        }

        return $badges;
    }

    /**
     * @return list<string>
     */
    private function layLoiThuongGap(int $hocVienId): array
    {
        $phienIds = PhienLuyenTap::query()->where('nguoi_dung_id', $hocVienId)->pluck('id');
        if ($phienIds->isEmpty()) {
            return [];
        }

        $texts = ChiTietLuyenTap::query()
            ->whereIn('phien_id', $phienIds)
            ->whereNotNull('chi_tiet_loi')
            ->where('chi_tiet_loi', '!=', '')
            ->orderByDesc('id')
            ->limit(80)
            ->pluck('chi_tiet_loi');

        return $texts->unique()->take(12)->values()->all();
    }

    /**
     * @return list<array{title: string, time: string, score: int}>
     */
    private function layLichSuLuyenTap(int $hocVienId): array
    {
        $phien = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $hocVienId)
            ->with(['baiHoc:id,tieu_de'])
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        return $phien->map(function (PhienLuyenTap $p) {
            $ref = $p->thoi_gian_ket_thuc ?? $p->thoi_gian_bat_dau ?? $p->ngay_tao;
            $carbon = $ref ? Carbon::parse($ref) : null;

            return [
                'title' => $p->baiHoc?->tieu_de ?? ('Bài học #' . $p->bai_hoc_id),
                'time' => $carbon ? $carbon->format('d/m/Y H:i') : '—',
                'score' => (int) ($p->tong_diem ?? 0),
            ];
        })->all();
    }

    private function formatTongThoiGianLuyen(int $hocVienId): string
    {
        $totalSeconds = PhienLuyenTap::query()
            ->where('nguoi_dung_id', $hocVienId)
            ->whereNotNull('thoi_gian_bat_dau')
            ->whereNotNull('thoi_gian_ket_thuc')
            ->get()
            ->sum(function (PhienLuyenTap $p) {
                try {
                    $start = Carbon::parse($p->thoi_gian_bat_dau);
                    $end = Carbon::parse($p->thoi_gian_ket_thuc);

                    return max(0, $end->diffInSeconds($start));
                } catch (\Throwable) {
                    return 0;
                }
            });

        $h = intdiv((int) $totalSeconds, 3600);
        $m = intdiv((int) $totalSeconds % 3600, 60);

        return $h > 0 ? "{$h}h {$m}p" : "{$m}p";
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function formatHoatDongGanNhat(?PhienLuyenTap $last): array
    {
        if (!$last) {
            return ['Chưa luyện', '—', 'text-muted'];
        }

        $ref = $last->thoi_gian_ket_thuc ?? $last->thoi_gian_bat_dau ?? $last->ngay_tao;
        if (!$ref) {
            return ['Chưa rõ', '—', 'text-secondary'];
        }

        try {
            $c = Carbon::parse($ref);
        } catch (\Throwable) {
            return ['Chưa rõ', '—', 'text-secondary'];
        }

        $now = Carbon::now();
        $timeStr = $c->format('H:i');

        if ($c->isSameDay($now)) {
            return ['Hôm nay', $timeStr, 'text-success'];
        }
        if ($c->isSameDay($now->copy()->subDay())) {
            return ['Hôm qua', $c->format('d/m H:i'), 'text-dark'];
        }
        $days = (int) $c->diffInDays($now);
        if ($days < 7) {
            return [$days . ' ngày trước', $c->format('d/m/Y H:i'), 'text-dark'];
        }
        if ($days < 30) {
            return ['Vài tuần trước', $c->format('d/m/Y'), 'text-warning'];
        }

        return ['Lâu chưa luyện', $c->format('d/m/Y'), 'text-danger'];
    }

    private function resolveAvatarUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    private function demHocVienCanChuY(Collection $hocVienIds): int
    {
        if ($hocVienIds->isEmpty()) {
            return 0;
        }

        $diemTbTheoHocVien = PhienLuyenTap::query()
            ->select('nguoi_dung_id', DB::raw('AVG(tong_diem) as diem_tb'))
            ->whereIn('nguoi_dung_id', $hocVienIds)
            ->whereNotNull('tong_diem')
            ->groupBy('nguoi_dung_id')
            ->get();

        return (int) $diemTbTheoHocVien->filter(function ($item): bool {
            return (float) $item->diem_tb < 60;
        })->count();
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private function thongKeLoiPhatAm(Collection $hocVienIds): array
    {
        if ($hocVienIds->isEmpty()) {
            return [0, 0, 0];
        }

        $phienIds = PhienLuyenTap::query()
            ->whereIn('nguoi_dung_id', $hocVienIds)
            ->pluck('id');

        if ($phienIds->isEmpty()) {
            return [0, 0, 0];
        }

        $loiAmDau = (int) ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->where('loi_am_dau', 1)->count();
        $loiVan = (int) ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->where('loi_van', 1)->count();
        $loiThanhDieu = (int) ChiTietLuyenTap::query()->whereIn('phien_id', $phienIds)->where('loi_thanh_dieu', 1)->count();

        return [$loiAmDau, $loiVan, $loiThanhDieu];
    }

    private function xacDinhLoiPhoBien(int $loiAmDau, int $loiVan, int $loiThanhDieu): string
    {
        $labels = [
            'Âm đầu' => $loiAmDau,
            'Vần' => $loiVan,
            'Thanh điệu' => $loiThanhDieu,
        ];

        arsort($labels);
        $label = array_key_first($labels);
        $max = $labels[$label] ?? 0;

        return $max > 0 ? 'Lỗi ' . $label : 'Chưa ghi nhận';
    }

    /**
     * @return array{labels:list<string>,data:list<int>}
     */
    private function duLieuPhienTheoNgay(Collection $hocVienIds): array
    {
        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $start = now()->startOfDay()->subDays($i);
            $end = (clone $start)->endOfDay();
            $labels[] = 'Thứ ' . $start->isoFormat('E');
            $data[] = (int) PhienLuyenTap::query()
                ->whereIn('nguoi_dung_id', $hocVienIds)
                ->where(function ($q) use ($start, $end): void {
                    $q->whereBetween('thoi_gian_bat_dau', [$start, $end])
                        ->orWhereBetween('thoi_gian_ket_thuc', [$start, $end])
                        ->orWhereBetween('ngay_tao', [$start, $end]);
                })
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array{labels:list<string>,data:list<int>}
     */
    private function duLieuPhienTheoTuan(Collection $hocVienIds): array
    {
        $labels = [];
        $data = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end = (clone $start)->endOfWeek();
            $labels[] = 'Tuần ' . $start->weekOfYear;
            $data[] = (int) PhienLuyenTap::query()
                ->whereIn('nguoi_dung_id', $hocVienIds)
                ->where(function ($q) use ($start, $end): void {
                    $q->whereBetween('thoi_gian_bat_dau', [$start, $end])
                        ->orWhereBetween('thoi_gian_ket_thuc', [$start, $end])
                        ->orWhereBetween('ngay_tao', [$start, $end]);
                })
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array{labels:list<string>,data:list<int>}
     */
    private function duLieuPhienTheoThang(Collection $hocVienIds): array
    {
        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end = (clone $start)->endOfMonth();
            $labels[] = 'Tháng ' . $start->month;
            $data[] = (int) PhienLuyenTap::query()
                ->whereIn('nguoi_dung_id', $hocVienIds)
                ->where(function ($q) use ($start, $end): void {
                    $q->whereBetween('thoi_gian_bat_dau', [$start, $end])
                        ->orWhereBetween('thoi_gian_ket_thuc', [$start, $end])
                        ->orWhereBetween('ngay_tao', [$start, $end]);
                })
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array{labels:list<string>,data:list<int>}
     */
    private function duLieuPhienTheoNam(Collection $hocVienIds): array
    {
        $labels = [];
        $data = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->startOfYear()->subYears($i);
            $end = (clone $start)->endOfYear();
            $labels[] = (string) $start->year;
            $data[] = (int) PhienLuyenTap::query()
                ->whereIn('nguoi_dung_id', $hocVienIds)
                ->where(function ($q) use ($start, $end): void {
                    $q->whereBetween('thoi_gian_bat_dau', [$start, $end])
                        ->orWhereBetween('thoi_gian_ket_thuc', [$start, $end])
                        ->orWhereBetween('ngay_tao', [$start, $end]);
                })
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return list<array{id:int,icon:string,tieu_de:string,mo_ta:string,thoi_gian:string}>
     */
    private function layHoatDongGanDay(Collection $hocVienIds): array
    {
        if ($hocVienIds->isEmpty()) {
            return [];
        }

        $phienGanDay = PhienLuyenTap::query()
            ->whereIn('nguoi_dung_id', $hocVienIds)
            ->with(['baiHoc:id,tieu_de'])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $hocVienMap = NguoiDung::query()
            ->whereIn('id', $hocVienIds)
            ->get(['id', 'ho_ten'])
            ->keyBy('id');

        return $phienGanDay->map(function (PhienLuyenTap $phien, int $idx) use ($hocVienMap): array {
            $hocVien = $hocVienMap->get($phien->nguoi_dung_id);
            $tenHocVien = $hocVien?->ho_ten ?? ('Học viên #' . $phien->nguoi_dung_id);
            $tieuDeBai = $phien->baiHoc?->tieu_de ?? ('Bài #' . $phien->bai_hoc_id);
            $diem = (int) ($phien->tong_diem ?? 0);
            $ref = $phien->thoi_gian_ket_thuc ?? $phien->thoi_gian_bat_dau ?? $phien->ngay_tao;
            $thoiGian = 'Vừa xong';
            if ($ref) {
                try {
                    $thoiGian = Carbon::parse($ref)->diffForHumans();
                } catch (\Throwable) {
                    $thoiGian = 'Vừa xong';
                }
            }

            return [
                'id' => $idx + 1,
                'icon' => $diem >= 80 ? '🌟' : '🎤',
                'tieu_de' => $tenHocVien . ' nộp bài',
                'mo_ta' => 'Bài: ' . $tieuDeBai . '. Điểm: ' . $diem . '/100.',
                'thoi_gian' => $thoiGian,
            ];
        })->all();
    }
}
