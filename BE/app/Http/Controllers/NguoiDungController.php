<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangNhapRequest;
use App\Http\Requests\DatLaiMatKhauRequest;
use App\Http\Requests\NguoiDungDangKyRequest;
use App\Http\Requests\QuenMatKhauRequest;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuenMatKhauMail;

class NguoiDungController extends Controller
{
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

        $client = new GoogleClient(['client_id' => $clientId]);
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $ho_ten = $payload['name'];
            $email = $payload['email'];

            $user = NguoiDung::where('email', $email)->first();

            if ($user) {
                $token = $user->createToken('token_nguoi_dung')->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => 'Đăng nhập thành công',
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
                    'trang_thai' => 1,
                ]);

                $token = $newUser->createToken('token_nguoi_dung')->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => 'Đăng ký và đăng nhập thành công!',
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

            // Xóa toàn bộ token cũ để mỗi lần đăng nhập chỉ dùng 1 thiết bị (Tùy chọn)
            // $user->tokens()->delete();

            return response()->json([
                'status'  => 1,
                'message' => 'Bạn đã đăng nhập thành công',
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
            return response()->json([
                'status'    => true,
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
