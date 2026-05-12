<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangNhapRequest;
use App\Http\Requests\DatLaiMatKhauRequest;
use App\Http\Requests\ChangeProfilePasswordRequest;
use App\Http\Requests\NguoiDungDangKyRequest;
use App\Http\Requests\QuenMatKhauRequest;
use App\Http\Requests\UpdateProfileAvatarRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\QuenMatKhauMail;

class NguoiDungController extends Controller
{
    private const BLOCKED_ACCOUNT_MESSAGE = 'Tài khoản của bạn đã vi phạm chính sách bảo mật của chúng tôi';
    private const ACTIVE_STATUS = 0;
    private const BLOCKED_STATUS = 1;

    private function isBlocked(NguoiDung $user): bool
    {
        return (int) ($user->trang_thai ?? self::ACTIVE_STATUS) === self::BLOCKED_STATUS;
    }

    private function resolveAvatarUrl(?string $raw): ?string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $raw)) {
            return $raw;
        }

        return url(Storage::url($raw));
    }

    private function buildProfilePayload($user): array
    {
        return [
            'id' => $user->id,
            'ho_va_ten' => $user->ho_ten,
            'email' => $user->email,
            'so_dien_thoai' => $user->sdt,
            'anh_dai_dien' => $user->anh_dai_dien,
            'anh_dai_dien_url' => $this->resolveAvatarUrl($user->anh_dai_dien),
            'dia_chi' => null,
            'chuc_vu' => [
                'ten_chuc_vu' => $user->vaiTro?->ten_vai_tro,
            ],
            'vai_tro_id' => $user->vai_tro_id,
        ];
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $user->load('vaiTro');

        return response()->json([
            'status' => true,
            'thong_tin' => $this->buildProfilePayload($user),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $user->update([
            'ho_ten' => $request->input('ho_va_ten'),
            'sdt' => $request->input('so_dien_thoai'),
        ]);

        $user->load('vaiTro');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công.',
            'thong_tin' => $this->buildProfilePayload($user),
        ]);
    }

    public function updateProfileAvatar(UpdateProfileAvatarRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $anhCu = $user->anh_dai_dien;
        if ($anhCu && !preg_match('/^https?:\/\//i', $anhCu) && Storage::disk('public')->exists($anhCu)) {
            Storage::disk('public')->delete($anhCu);
        }

        $uploadedFile = cloudinary()->uploadApi()->upload(
            $request->file('anh_dai_dien')->getRealPath(),
            ['folder' => 'avatars']
        );

        $user->anh_dai_dien = $uploadedFile['secure_url'];
        $user->save();
        $user->load('vaiTro');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật ảnh đại diện thành công.',
            'thong_tin' => $this->buildProfilePayload($user),
        ]);
    }

    public function changeProfilePassword(ChangeProfilePasswordRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        if (!Hash::check($request->old_password, $user->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu hiện tại không đúng.',
            ], 422);
        }

        $user->mat_khau = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }

    public function logOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => "Đăng xuất thành công",
        ]);
    }
    public function forgotPassword(QuenMatKhauRequest $request)
    {
        try {
            $email = $request->validated('email');
            $user = NguoiDung::where('email', $email)->firstOrFail();

            $otp = (string) random_int(100000, 999999);
            $user->hash_reset = $otp;
            $user->save();

            Mail::to($user->email)->send(new QuenMatKhauMail($user->ho_ten, $otp));

            return response()->json([
                'status'  => true,
                'message' => 'Mã xác nhận đã được gửi! Vui lòng kiểm tra hộp thư email của bạn.',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi gửi mail quên mật khẩu:', [
                'email' => $request->validated('email'),
                'chi_tiet_loi' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Hệ thống đang bận hoặc có lỗi khi gửi email. Vui lòng thử lại sau ít phút.',
            ], 500);
        }
    }
    public function resetPassword(DatLaiMatKhauRequest $request)
    {
        $data = $request->validated();

        try {
            // 1. Tìm user có email và mã OTP khớp nhau
            $user = NguoiDung::where('email', $data['email'])
                ->where('hash_reset', $data['otp'])
                ->first();

            // 2. Nếu không tìm thấy (OTP sai hoặc Email sai)
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Mã xác nhận không chính xác. Vui lòng kiểm tra lại!'
                ]);
            }

            // ========================================================
            // 3. KIỂM TRA THỜI GIAN HẾT HẠN CỦA MÃ OTP (Giới hạn 5 phút)
            // ========================================================
            // Tính số phút chênh lệch từ lúc tạo mã (updated_at) đến hiện tại (now)
            $soPhutDaQua = now()->diffInMinutes($user->updated_at);

            if ($soPhutDaQua >= 5) {
                // Mã đã hết hạn -> Xóa mã cũ đi để bảo mật
                $user->hash_reset = null;
                $user->save();

                return response()->json([
                    'status'  => false,
                    'message' => 'Mã xác nhận đã hết hạn (quá 5 phút). Vui lòng gửi yêu cầu cấp lại mã mới!'
                ]);
            }

            // 4. Nếu OTP đúng và còn hạn -> Tiến hành đổi mật khẩu
            $user->mat_khau = Hash::make($data['new_password']);
            $user->hash_reset = null; // Xóa mã OTP đi sau khi dùng xong
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Đổi mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
            ], 500);
        }
    }
    public function register(NguoiDungDangKyRequest $request)
    {
        try {
            $nguoiDung = NguoiDung::create([
                'ho_ten'     => $request->ho_ten,
                'email'      => $request->email,
                'mat_khau'   => Hash::make($request->password),
                'sdt'        => preg_replace('/[^0-9]/', '', $request->sdt),
                'ngay_sinh'  => $request->ngay_sinh,
                'vai_tro_id' => 3,
                'trang_thai' => self::ACTIVE_STATUS,
            ]);

            event(new \Illuminate\Auth\Events\Registered($nguoiDung));

            $token = $nguoiDung->createToken('token_nguoi_dung')->plainTextToken;

            return response()->json([
                'status'  => 1,
                'message' => 'Đăng ký tài khoản thành công! Vui lòng đăng nhập để tiếp tục.',
                'data'    => [
                    'id'     => $nguoiDung->id,
                    'email'  => $nguoiDung->email,
                    'ho_ten' => $nguoiDung->ho_ten,
                    'token'  => $token,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => 'Hệ thống đang bảo trì. Vui lòng thử lại sau!',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    public function loginGoogle(Request $request)
    {
        $idToken = $request->input('id_token') ?? $request->input('credential');
        if (! is_string($idToken) || $idToken === '') {
            return response()->json([
                'status'  => false,
                'message' => 'Thiếu mã đăng nhập Google. Vui lòng thử đăng nhập lại.',
            ], 422);
        }

        $clientId = env('GOOGLE_CLIENT_ID');
        if (! is_string($clientId) || $clientId === '') {
            return response()->json([
                'status'  => false,
                'message' => 'Hệ thống chưa cấu hình GOOGLE_CLIENT_ID.',
            ], 500);
        }

        try {
            $client = new GoogleClient(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($idToken);
        } catch (\Exception $e) {
            Log::error('Google OAuth verification failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            // Fallback for development: decode JWT manually without verification
            if (app()->environment('local', 'dev')) {
                Log::warning('Using development JWT decode fallback (no verification)');
                $payload = $this->decodeJwtForDevelopment($idToken);
                if (!$payload) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Không thể xác thực token Google. Token không hợp lệ.',
                    ], 401);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Không thể xác thực với Google. Vui lòng kiểm tra kết nối internet hoặc thử đăng nhập thường.',
                ], 503);
            }
        }

        if ($payload) {
            $ho_ten = $payload['name'];
            $email = $payload['email'];

            $user = NguoiDung::where('email', $email)->first();

            if ($user) {
                if ($this->isBlocked($user)) {
                    return response()->json([
                        'status'  => false,
                        'message' => self::BLOCKED_ACCOUNT_MESSAGE,
                    ], 403);
                }

                $token = $user->createToken('token_nguoi_dung')->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => 'Đăng nhập thành công',
                    'id'      => $user->id,
                    'ho_ten'  => $user->ho_ten,
                    'email'   => $user->email,
                    'anh_dai_dien'  => $user->anh_dai_dien ?: ($payload['picture'] ?? null),
                    'vai_tro_id' => $user->vai_tro_id,
                    'ten_vai_tro' => $user->vaiTro ? $user->vaiTro->ten_vai_tro : null,
                    'token'   => $token,
                ]);
            } else {
                $newUser = NguoiDung::create([
                    'ho_ten'     => $ho_ten,
                    'email'      => $email,
                    'mat_khau'   => Hash::make(\Illuminate\Support\Str::random(16)),
                    'sdt'        => null,
                    'ngay_sinh'  => null,
                    'vai_tro_id' => 3,
                    'trang_thai' => self::ACTIVE_STATUS,
                ]);

                $token = $newUser->createToken('token_nguoi_dung')->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => 'Đăng ký và đăng nhập thành công!',
                    'id'      => $newUser->id,
                    'ho_ten'  => $newUser->ho_ten,
                    'email'   => $newUser->email,
                    'anh_dai_dien'  => $payload['picture'] ?? null,
                    'vai_tro_id' => $newUser->vai_tro_id,
                    'ten_vai_tro' => $newUser->vaiTro ? $newUser->vaiTro->ten_vai_tro : null,
                    'token'   => $token,
                ]);
            }
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Token Google không hợp lệ hoặc đã hết hạn.',
            ], 401);
        }
    }

    /**
     * Decode JWT token manually for development when Google API is not accessible
     * WARNING: This is NOT secure and should only be used in development
     */
    private function decodeJwtForDevelopment($idToken)
    {
        try {
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                return null;
            }

            // Decode payload (2nd part)
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            return $payload;
        } catch (\Exception $e) {
            Log::warning('Failed to decode JWT manually', ['error' => $e->getMessage()]);
            return null;
        }
    }
    public function login(DangNhapRequest $request)
    {
        $secretKey = config('services.recaptcha.secret');
        if (!$secretKey) {
            return response()->json([
                'status' => 0,
                'message' => 'Hệ thống chưa cấu hình reCAPTCHA.',
            ], 500);
        }

        try {
            $res = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $request->code,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Không thể xác thực reCAPTCHA. Vui lòng thử lại.',
            ], 500);
        }

        if (!$res->ok() || !$res->json('success')) {
            return response()->json([
                'status' => 0,
                'message' => 'Xác thực reCAPTCHA không hợp lệ.',
            ], 422);
        }

        $user = NguoiDung::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->mat_khau)) {
            if ($this->isBlocked($user)) {
                return response()->json([
                    'status'  => 0,
                    'message' => self::BLOCKED_ACCOUNT_MESSAGE,
                ], 403);
            }

            // Xóa toàn bộ token cũ để mỗi lần đăng nhập chỉ dùng 1 thiết bị (Tùy chọn)
            // $user->tokens()->delete();

            return response()->json([
                'status'  => 1,
                'message' => 'Bạn đã đăng nhập thành công',
                'id'      => $user->id,
                'ho_ten'  => $user->ho_ten,
                'email'   => $user->email,
                'anh_dai_dien'  => $user->anh_dai_dien,
                'vai_tro_id' => $user->vai_tro_id,
                'ten_vai_tro' => $user->vaiTro ? $user->vaiTro->ten_vai_tro : null,
                'token'   => $user->createToken('token_nguoi_dung')->plainTextToken,
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => 'Tài khoản hoặc mật khẩu không đúng.'
            ], 401);
        }
    }

    public function checkToken()
    {
        $userLogin = Auth::guard('sanctum')->user();
        if ($userLogin) {
            if ($this->isBlocked($userLogin)) {
                return response()->json([
                    'status'    => false,
                    'message'   => self::BLOCKED_ACCOUNT_MESSAGE,
                ], 403);
            }

            return response()->json([
                'status'    => true,
                'id'        => $userLogin->id,
                'ho_ten'    => $userLogin->ho_ten,
                'anh_dai_dien'    => $userLogin->anh_dai_dien,
                'email'      => $userLogin->email,
                'vai_tro_id' => $userLogin->vai_tro_id,
                'ten_vai_tro' => $userLogin->vaiTro ? $userLogin->vaiTro->ten_vai_tro : null,
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Token không hợp lệ'
            ], 401);
        }
    }
}
