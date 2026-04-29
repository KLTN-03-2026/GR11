<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * $roleId là tham số bạn sẽ truyền vào từ Route
     */
    public function handle(Request $request, Closure $next, $roleId): Response
    {
        $user = $request->user();

        if ($user && (int) ($user->is_block ?? 0) === 1) {
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã vi phạm chính sách bảo mật của chúng tôi'
            ], 403);
        }

        // Kiểm tra xem vai_tro_id của user có khớp với role truyền vào không
        if ($user && $user->vai_tro_id == $roleId) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Bạn không có quyền truy cập.'
        ], 403);
    }
}
