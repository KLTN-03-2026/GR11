<?php

namespace App\Http\Controllers;

use App\Http\Requests\DangKyHoSoGiaoVienRequest;
use App\Mail\HoSoGiaoVienDuyetMail;
use App\Mail\HoSoGiaoVienTuChoiMail;
use App\Models\HoSoGiaoVien;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HoSoGiaoVienController extends Controller
{
    /**
     * Người dùng nộp hồ sơ đăng ký giáo viên
     * Validation được xử lý hoàn toàn trong DangKyHoSoGiaoVienRequest
     */
    public function store(DangKyHoSoGiaoVienRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        // Chỉ học viên (vai_tro_id = 3) mới được đăng ký
        if ((int) $user->vai_tro_id !== NguoiDung::ROLE_USER) {
            return response()->json(['status' => false, 'message' => 'Tài khoản của bạn không phải học viên.'], 403);
        }

        // Kiểm tra đã có hồ sơ chờ duyệt / đã duyệt chưa
        $existing = HoSoGiaoVien::where('nguoi_dung_id', $user->id)
            ->whereIn('trang_thai', [HoSoGiaoVien::TRANG_THAI_CHO_DUYET, HoSoGiaoVien::TRANG_THAI_DA_DUYET])
            ->first();

        if ($existing) {
            $msg = $existing->trang_thai === HoSoGiaoVien::TRANG_THAI_DA_DUYET
                ? 'Hồ sơ của bạn đã được duyệt trước đó.'
                : 'Bạn đã có hồ sơ đang chờ duyệt.';
            return response()->json(['status' => false, 'message' => $msg], 422);
        }

        // Tất cả dữ liệu đã được validate bởi DangKyHoSoGiaoVienRequest

        // Lưu các file
        $anhDaiDien   = $request->hasFile('anh_dai_dien') ? $request->file('anh_dai_dien')->store('ho-so-giao-vien/avatar', 'public') : null;
        $cccdTruoc    = $request->file('cccd_mat_truoc')->store('ho-so-giao-vien/cccd', 'public');
        $cccdSau      = $request->file('cccd_mat_sau')->store('ho-so-giao-vien/cccd', 'public');
        $bangCap      = $request->file('bang_cap')->store('ho-so-giao-vien/bang-cap', 'public');

        $hoSo = HoSoGiaoVien::create([
            'nguoi_dung_id'  => $user->id,
            'ho_ten'         => $request->input('ho_ten'),
            'email'          => $user->email,
            'so_dien_thoai'  => $request->input('so_dien_thoai'),
            'anh_dai_dien'   => $anhDaiDien,
            'cccd_mat_truoc' => $cccdTruoc,
            'cccd_mat_sau'   => $cccdSau,
            'bang_cap'       => $bangCap,
            'chuyen_mon'     => $request->input('chuyen_mon'),
            'mo_ta'          => $request->input('mo_ta'),
            'trang_thai'     => HoSoGiaoVien::TRANG_THAI_CHO_DUYET,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Hồ sơ đăng ký giáo viên đã được gửi thành công! Vui lòng chờ Admin xét duyệt.',
            'data'    => $this->formatHoSo($hoSo),
        ], 201);
    }

    /**
     * Người dùng kiểm tra trạng thái hồ sơ của mình
     */
    public function myStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        $hoSo = HoSoGiaoVien::where('nguoi_dung_id', $user->id)
            ->latest()
            ->first();

        if (!$hoSo) {
            return response()->json([
                'status'  => true,
                'data'    => null,
                'message' => 'Chưa có hồ sơ đăng ký.',
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => $this->formatHoSo($hoSo),
        ]);
    }

    // ==================== ADMIN ====================

    /**
     * Admin lấy danh sách tất cả hồ sơ
     */
    public function index(Request $request)
    {
        $trangThai = $request->query('trang_thai');

        $query = HoSoGiaoVien::with('nguoiDung')->orderByDesc('id');

        if ($trangThai !== null && $trangThai !== '') {
            $query->where('trang_thai', (int) $trangThai);
        }

        $data = $query->get()->map(fn($hs) => $this->formatHoSo($hs));

        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Admin duyệt hồ sơ → nâng vai trò + cập nhật profile + gửi email
     */
    public function approve(Request $request, $id)
    {
        $hoSo = HoSoGiaoVien::with('nguoiDung')->find($id);
        if (!$hoSo) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy hồ sơ.'], 404);
        }

        if ($hoSo->trang_thai !== HoSoGiaoVien::TRANG_THAI_CHO_DUYET) {
            return response()->json(['status' => false, 'message' => 'Hồ sơ này đã được xử lý trước đó.'], 422);
        }

        // Cập nhật trạng thái hồ sơ
        $hoSo->trang_thai    = HoSoGiaoVien::TRANG_THAI_DA_DUYET;
        $hoSo->ghi_chu_admin = null;
        $hoSo->save();

        // Nâng vai trò tài khoản lên Giáo viên + cập nhật thông tin profile
        $user = $hoSo->nguoiDung;
        if ($user) {
            $user->vai_tro_id = NguoiDung::ROLE_TEACHER;

            // Cập nhật họ tên nếu trống
            if (empty($user->ho_ten)) {
                $user->ho_ten = $hoSo->ho_ten;
            }

            // Cập nhật số điện thoại nếu có và đang trống
            if (!empty($hoSo->so_dien_thoai) && empty($user->sdt)) {
                $user->sdt = $hoSo->so_dien_thoai;
            }

            // Cập nhật ảnh đại diện từ hồ sơ nếu người dùng chưa có
            if (!empty($hoSo->anh_dai_dien) && empty($user->anh_dai_dien)) {
                $user->anh_dai_dien = $hoSo->anh_dai_dien;
            }

            $user->save();
        }

        // Gửi email xác nhận
        try {
            Mail::to($hoSo->email)->send(new HoSoGiaoVienDuyetMail($hoSo->ho_ten));
        } catch (\Throwable $e) {
            Log::error('Lỗi gửi mail duyệt hồ sơ GV:', ['id' => $id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đã duyệt hồ sơ và gửi email thông báo cho ' . $hoSo->ho_ten . '.',
            'data'    => $this->formatHoSo($hoSo->fresh()),
        ]);
    }

    /**
     * Admin từ chối hồ sơ + ghi lý do → gửi email
     */
    public function reject(Request $request, $id)
    {
        $hoSo = HoSoGiaoVien::with('nguoiDung')->find($id);
        if (!$hoSo) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy hồ sơ.'], 404);
        }

        if ($hoSo->trang_thai !== HoSoGiaoVien::TRANG_THAI_CHO_DUYET) {
            return response()->json(['status' => false, 'message' => 'Hồ sơ này đã được xử lý trước đó.'], 422);
        }

        $request->validate([
            'ghi_chu' => 'required|string|max:1000',
        ], [
            'ghi_chu.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $hoSo->trang_thai    = HoSoGiaoVien::TRANG_THAI_TU_CHOI;
        $hoSo->ghi_chu_admin = $request->input('ghi_chu');
        $hoSo->save();

        // Gửi email thông báo từ chối
        try {
            Mail::to($hoSo->email)->send(new HoSoGiaoVienTuChoiMail($hoSo->ho_ten, $hoSo->ghi_chu_admin));
        } catch (\Throwable $e) {
            Log::error('Lỗi gửi mail từ chối hồ sơ GV:', ['id' => $id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đã từ chối hồ sơ và gửi email thông báo cho ' . $hoSo->ho_ten . '.',
            'data'    => $this->formatHoSo($hoSo->fresh()),
        ]);
    }

    // ==================== HELPER ====================

    private function storageUrl(?string $path): ?string
    {
        if (!$path) return null;
        return url(Storage::url($path));
    }

    private function formatHoSo(HoSoGiaoVien $hs): array
    {
        $trangThaiLabel = match ((int) $hs->trang_thai) {
            HoSoGiaoVien::TRANG_THAI_DA_DUYET => 'Đã duyệt',
            HoSoGiaoVien::TRANG_THAI_TU_CHOI  => 'Từ chối',
            default                            => 'Chờ duyệt',
        };

        return [
            'id'                 => $hs->id,
            'nguoi_dung_id'      => $hs->nguoi_dung_id,
            'ho_ten'             => $hs->ho_ten,
            'email'              => $hs->email,
            'so_dien_thoai'      => $hs->so_dien_thoai,
            // Profile image
            'anh_dai_dien'       => $hs->anh_dai_dien,
            'anh_dai_dien_url'   => $this->storageUrl($hs->anh_dai_dien),
            // KYC
            'cccd_mat_truoc'     => $hs->cccd_mat_truoc,
            'cccd_mat_truoc_url' => $this->storageUrl($hs->cccd_mat_truoc),
            'cccd_mat_sau'       => $hs->cccd_mat_sau,
            'cccd_mat_sau_url'   => $this->storageUrl($hs->cccd_mat_sau),
            // Qualifications
            'bang_cap'           => $hs->bang_cap,
            'bang_cap_url'       => $this->storageUrl($hs->bang_cap),
            // Other
            'chuyen_mon'         => $hs->chuyen_mon,
            'mo_ta'              => $hs->mo_ta,
            'trang_thai'         => (int) $hs->trang_thai,
            'trang_thai_label'   => $trangThaiLabel,
            'ghi_chu_admin'      => $hs->ghi_chu_admin,
            'created_at'         => $hs->created_at?->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
            'updated_at'         => $hs->updated_at?->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
        ];
    }
}
