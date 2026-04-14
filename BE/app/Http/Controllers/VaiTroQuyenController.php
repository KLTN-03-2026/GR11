<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncVaiTroQuyenRequest;
use App\Models\Quyen;
use App\Models\VaiTro;
use App\Models\VaiTroQuyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VaiTroQuyenController extends Controller
{
    public function getData(): JsonResponse
    {
        $vaiTros = VaiTro::query()->orderBy('id')->get();
        $quyens = Quyen::query()->orderBy('id')->get();
        $phanQuyens = VaiTroQuyen::query()->get(['vai_tro_id', 'quyen_id']);

        $mapping = [];
        foreach ($phanQuyens as $item) {
            $mapping[$item->vai_tro_id][] = $item->quyen_id;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'vai_tros' => $vaiTros,
                'quyens' => $quyens,
                'mapping' => $mapping,
            ],
        ]);
    }

    public function sync(SyncVaiTroQuyenRequest $request): JsonResponse
    {
        $vaiTroId = (int) $request->vai_tro_id;
        $quyenIds = collect($request->input('quyen_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($vaiTroId, $quyenIds): void {
            VaiTroQuyen::where('vai_tro_id', $vaiTroId)->delete();

            if (!empty($quyenIds)) {
                $rows = array_map(function ($quyenId) use ($vaiTroId) {
                    return [
                        'vai_tro_id' => $vaiTroId,
                        'quyen_id' => $quyenId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $quyenIds);

                VaiTroQuyen::insert($rows);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Đồng bộ quyền cho vai trò thành công.',
        ]);
    }
}
