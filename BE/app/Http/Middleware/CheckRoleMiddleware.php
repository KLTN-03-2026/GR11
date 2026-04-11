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
