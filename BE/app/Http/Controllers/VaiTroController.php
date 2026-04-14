<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVaiTroRequest;
use App\Models\VaiTro;
use Illuminate\Http\JsonResponse;

class VaiTroController extends Controller
{
    public function getData(): JsonResponse
    {
        $data = VaiTro::query()
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function store(StoreVaiTroRequest $request): JsonResponse
    {
        $vaiTro = VaiTro::create([
            'ten_vai_tro' => $request->ten_vai_tro,
            'mo_ta' => $request->mo_ta,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã tạo vai trò thành công.',
            'data' => $vaiTro,
        ]);
    }
}
