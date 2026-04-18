<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyTeacherTuVungRequest;
use App\Http\Requests\ListTeacherTuVungRequest;
use App\Http\Requests\StoreTeacherTuVungRequest;
use App\Http\Requests\UpdateTeacherTuVungRequest;
use App\Models\BaiHoc;
use App\Models\TuVung;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeacherTuVungController extends Controller
{
    public function index(ListTeacherTuVungRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::query()->find($id);
        if (!$baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học.',
            ], 404);
        }

        $tuVungs = TuVung::query()
            ->where('bai_hoc_id', $id)
            ->orderBy('thu_tu')
            ->orderBy('id')
            ->get();

        $data = $tuVungs->map(fn (TuVung $tv) => $this->mapTuVungRow($tv));

        return response()->json([
            'status' => true,
            'bai_hoc' => [
                'id' => $baiHoc->id,
                'tieu_de' => $baiHoc->tieu_de,
            ],
            'data' => $data,
        ]);
    }

    public function store(StoreTeacherTuVungRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::query()->find($id);
        if (!$baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học.',
            ], 404);
        }

        $tuVung = DB::transaction(function () use ($request, $id) {
            $thuTu = $request->input('thu_tu');
            if ($thuTu === null || $thuTu === '') {
                $max = TuVung::query()->where('bai_hoc_id', $id)->max('thu_tu');
                $thuTu = $max === null ? 1 : (int) $max + 1;
            } else {
                $thuTu = (int) $thuTu;
                TuVung::query()
                    ->where('bai_hoc_id', $id)
                    ->where('thu_tu', '>=', $thuTu)
                    ->update(['thu_tu' => DB::raw('thu_tu + 1')]);
            }

            return TuVung::query()->create([
                'bai_hoc_id' => $id,
                'tu_chuan' => $request->tu_chuan,
                'phien_am' => $request->phien_am,
                'cap_do' => $request->cap_do,
                'hinh_anh_url' => $request->hinh_anh_url,
                'am_thanh_mau_url' => $request->am_thanh_mau_url,
                'thu_tu' => $thuTu,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm từ vựng thành công.',
            'data' => $this->mapTuVungRow($tuVung),
        ], 201);
    }

    public function update(UpdateTeacherTuVungRequest $request, int $id): JsonResponse
    {
        $tuVung = TuVung::query()->find($id);
        if (!$tuVung) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy từ vựng.',
            ], 404);
        }

        DB::transaction(function () use ($request, $tuVung) {
            $newThuTu = (int) $request->input('thu_tu', $tuVung->thu_tu);
            $oldThuTu = (int) $tuVung->thu_tu;
            $baiHocId = (int) $tuVung->bai_hoc_id;

            if ($newThuTu !== $oldThuTu) {
                if ($newThuTu < $oldThuTu) {
                    TuVung::query()
                        ->where('bai_hoc_id', $baiHocId)
                        ->where('id', '!=', $tuVung->id)
                        ->whereBetween('thu_tu', [$newThuTu, $oldThuTu - 1])
                        ->update(['thu_tu' => DB::raw('thu_tu + 1')]);
                } else {
                    TuVung::query()
                        ->where('bai_hoc_id', $baiHocId)
                        ->where('id', '!=', $tuVung->id)
                        ->whereBetween('thu_tu', [$oldThuTu + 1, $newThuTu])
                        ->update(['thu_tu' => DB::raw('thu_tu - 1')]);
                }
            }

            $tuVung->update([
                'tu_chuan' => $request->tu_chuan,
                'phien_am' => $request->phien_am,
                'cap_do' => $request->cap_do,
                'hinh_anh_url' => $request->hinh_anh_url,
                'am_thanh_mau_url' => $request->am_thanh_mau_url,
                'thu_tu' => $newThuTu,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật từ vựng thành công.',
            'data' => $this->mapTuVungRow($tuVung->fresh()),
        ]);
    }

    public function destroy(DestroyTeacherTuVungRequest $request, int $id): JsonResponse
    {
        $tuVung = TuVung::query()->find($id);
        if (!$tuVung) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy từ vựng.',
            ], 404);
        }

        $tuVung->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa từ vựng thành công.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapTuVungRow(TuVung $tv): array
    {
        return [
            'id' => $tv->id,
            'bai_hoc_id' => $tv->bai_hoc_id,
            'tu_chuan' => $tv->tu_chuan,
            'phien_am' => $tv->phien_am,
            'cap_do' => $tv->cap_do,
            'hinh_anh_url' => $this->resolveMediaUrl($tv->hinh_anh_url),
            'am_thanh_mau_url' => $this->resolveMediaUrl($tv->am_thanh_mau_url),
            'thu_tu' => $tv->thu_tu,
        ];
    }

    private function resolveMediaUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }
}
