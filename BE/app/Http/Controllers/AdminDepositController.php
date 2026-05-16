<?php

namespace App\Http\Controllers;

use App\Models\DonNapTien;
use App\Models\GiaoDichVi;
use App\Models\ThongBao;
use App\Models\Vi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDepositController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $onlyPending = ! filter_var($request->query('all'), FILTER_VALIDATE_BOOLEAN);

        $q = DonNapTien::query()
            ->with(['nguoiDung:id,ho_ten,email'])
            ->orderByDesc('created_at');

        if ($onlyPending) {
            $q->where('trang_thai', 'cho_thanh_toan');
        }

        $perPage = min(100, max(5, (int) $request->query('per_page', 20)));
        $paginator = $q->paginate($perPage);

        $data = $paginator->getCollection()->map(function (DonNapTien $d) {
            $u = $d->nguoiDung;
            $meta = $d->duLieuCallbackArray();

            return [
                'id' => $d->id,
                'ma_don' => $d->ma_don,
                'so_tien' => (int) $d->so_tien,
                'trang_thai' => $d->trang_thai,
                'created_at' => $d->created_at?->toIso8601String(),
                'chung_tu_ck_urls' => $meta['chung_tu_ck_urls'] ?? [],
                'ly_do_tu_choi' => $meta['ly_do_tu_choi'] ?? null,
                'nguoi_dung' => $u ? [
                    'id' => $u->id,
                    'ho_ten' => $u->ho_ten,
                    'email' => $u->email,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function confirm(Request $request, int $id): JsonResponse
    {
        try {
            DB::transaction(function () use ($id): void {
                $don = DonNapTien::query()->whereKey($id)->lockForUpdate()->first();
                if (! $don) {
                    throw new \RuntimeException('NOT_FOUND');
                }
                if ($don->trang_thai !== 'cho_thanh_toan') {
                    throw new \RuntimeException('INVALID_STATE');
                }

                $vi = Vi::query()
                    ->where('nguoi_dung_id', $don->nguoi_dung_id)
                    ->lockForUpdate()
                    ->first();

                if (! $vi) {
                    Vi::query()->create([
                        'nguoi_dung_id' => $don->nguoi_dung_id,
                        'so_du' => 0,
                    ]);
                    $vi = Vi::query()
                        ->where('nguoi_dung_id', $don->nguoi_dung_id)
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                $truoc = (int) $vi->so_du;
                $sau = $truoc + (int) $don->so_tien;
                $vi->so_du = $sau;
                $vi->save();

                GiaoDichVi::query()->create([
                    'vi_id' => $vi->id,
                    'loai_giao_dich' => 'nap_tien',
                    'chieu_giao_dich' => 'in',
                    'so_tien' => (int) $don->so_tien,
                    'so_du_truoc' => $truoc,
                    'so_du_sau' => $sau,
                    'tham_chieu_type' => DonNapTien::class,
                    'tham_chieu_id' => $don->id,
                    'ghi_chu' => 'Nạp tiền '.$don->ma_don,
                ]);

                $don->trang_thai = 'thanh_cong';
                $don->save();

                ThongBao::query()->create([
                    'nguoi_nhan_id' => $don->nguoi_dung_id,
                    'tieu_de' => 'Nạp tiền thành công',
                    'noi_dung' => 'Đơn '.$don->ma_don.' đã được xác nhận. Số tiền '.number_format((int) $don->so_tien, 0, ',', ' ').' VNĐ đã được cộng vào ví.',
                    'loai' => 'vi_nap_tien',
                    'duong_dan' => '/profile',
                    'da_doc' => 0,
                ]);
            });
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();

            return response()->json([
                'status' => false,
                'message' => match ($code) {
                    'NOT_FOUND' => 'Không tìm thấy đơn nạp.',
                    'INVALID_STATE' => 'Đơn không ở trạng thái chờ thanh toán hoặc đã được xử lý.',
                    default => 'Không thể xác nhận đơn.',
                },
            ], $code === 'NOT_FOUND' ? 404 : 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã xác nhận nạp tiền và cộng ví.',
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'ly_do' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($id, $validated): void {
                $don = DonNapTien::query()->whereKey($id)->lockForUpdate()->first();
                if (! $don) {
                    throw new \RuntimeException('NOT_FOUND');
                }
                if (! in_array($don->trang_thai, ['cho_thanh_toan'], true)) {
                    throw new \RuntimeException('INVALID_STATE');
                }

                $meta = [];
                if ($don->du_lieu_callback) {
                    $decoded = json_decode($don->du_lieu_callback, true);
                    if (is_array($decoded)) {
                        $meta = $decoded;
                    }
                }
                if (! empty($validated['ly_do'])) {
                    $meta['ly_do_tu_choi'] = $validated['ly_do'];
                }
                $don->du_lieu_callback = json_encode($meta, JSON_UNESCAPED_UNICODE);
                $don->trang_thai = 'that_bai';
                $don->save();

                $lyDo = $validated['ly_do'] ?? 'Đơn nạp tiền không được duyệt.';
                ThongBao::query()->create([
                    'nguoi_nhan_id' => $don->nguoi_dung_id,
                    'tieu_de' => 'Đơn nạp tiền bị từ chối',
                    'noi_dung' => 'Đơn '.$don->ma_don.' đã bị từ chối. '.$lyDo,
                    'loai' => 'vi_nap_tien_tu_choi',
                    'duong_dan' => '/profile',
                    'da_doc' => 0,
                ]);
            });
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();

            return response()->json([
                'status' => false,
                'message' => match ($code) {
                    'NOT_FOUND' => 'Không tìm thấy đơn nạp.',
                    'INVALID_STATE' => 'Chỉ có thể từ chối đơn đang chờ thanh toán.',
                    default => 'Không thể từ chối đơn.',
                },
            ], $code === 'NOT_FOUND' ? 404 : 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã từ chối đơn nạp tiền.',
        ]);
    }
}
