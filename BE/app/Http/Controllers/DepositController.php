<?php

namespace App\Http\Controllers;

use App\Models\DonNapTien;
use App\Services\DepositDonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function __construct(
        private readonly DepositDonService $depositDonService,
    ) {}

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $validated = $request->validate([
            'so_tien' => ['required', 'integer', 'min:10000'],
            'phuong_thuc' => ['nullable', 'string', 'max:50'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ]);

        $created = $this->depositDonService->create(
            $user,
            (int) $validated['so_tien'],
            $validated['phuong_thuc'] ?? null,
            $validated['ghi_chu'] ?? null,
        );

        $don = $created['don'];

        return response()->json([
            'status' => true,
            'message' => 'Đơn nạp đã tạo. Quét mã QR để chuyển khoản (ghi đúng mã đơn trong nội dung CK).',
            'ma_don' => $don->ma_don,
            'so_tien' => (int) $don->so_tien,
            'trang_thai' => $don->trang_thai,
            'qr_url' => $created['qr_url'],
            'vietqr_configured' => $created['vietqr_configured'],
            'qr_account_name' => $created['qr_account_name'] ?: null,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $perPage = min(50, max(5, (int) $request->query('per_page', 15)));

        $paginator = DonNapTien::query()
            ->where('nguoi_dung_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $data = $paginator->getCollection()->map(function (DonNapTien $d) {
            $meta = $d->duLieuCallbackArray();

            return [
                'id' => $d->id,
                'ma_don' => $d->ma_don,
                'so_tien' => (int) $d->so_tien,
                'trang_thai' => $d->trang_thai,
                'created_at' => $d->created_at?->toIso8601String(),
                'chung_tu_ck_urls' => $meta['chung_tu_ck_urls'] ?? [],
                'ly_do_tu_choi' => $meta['ly_do_tu_choi'] ?? null,
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

    /**
     * Gắn ảnh chứng từ CK vào JSON `du_lieu_callback.chung_tu_ck_urls` (không migration).
     * Cho phép khi đơn chờ duyệt hoặc đã từ chối (khiếu nại / đối soát hoàn tiền ngoài app).
     */
    public function uploadChungTu(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $uploadedFiles = $validated['files'];
        if (! is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        try {
            $result = DB::transaction(function () use ($user, $id, $uploadedFiles): array {
                $don = DonNapTien::query()
                    ->whereKey($id)
                    ->where('nguoi_dung_id', $user->id)
                    ->lockForUpdate()
                    ->first();
                if (! $don) {
                    throw new \RuntimeException('NOT_FOUND');
                }
                if (! in_array($don->trang_thai, ['cho_thanh_toan', 'that_bai'], true)) {
                    throw new \RuntimeException('INVALID_STATE');
                }

                $meta = $don->duLieuCallbackArray();
                $existing = $meta['chung_tu_ck_urls'] ?? [];
                if (! is_array($existing)) {
                    $existing = [];
                }
                $cap = 12;
                $room = max(0, $cap - count($existing));
                if ($room <= 0) {
                    throw new \RuntimeException('LIMIT');
                }

                $files = array_slice($uploadedFiles, 0, $room);
                $newUrls = [];
                foreach ($files as $file) {
                    $uploaded = cloudinary()->uploadApi()->upload(
                        $file->getRealPath(),
                        ['folder' => 'deposit_chung_tu/'.$don->id]
                    );
                    $newUrls[] = $uploaded['secure_url'];
                }
                $meta['chung_tu_ck_urls'] = array_values(array_unique(array_merge($existing, $newUrls)));
                $don->du_lieu_callback = json_encode($meta, JSON_UNESCAPED_UNICODE);
                $don->save();

                return ['chung_tu_ck_urls' => $meta['chung_tu_ck_urls']];
            });
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();

            return response()->json([
                'status' => false,
                'message' => match ($code) {
                    'NOT_FOUND' => 'Không tìm thấy đơn nạp.',
                    'INVALID_STATE' => 'Chỉ có thể đính kèm chứng từ cho đơn đang chờ hoặc đã từ chối.',
                    'LIMIT' => 'Đã đủ số ảnh chứng từ cho đơn này (tối đa 12).',
                    default => 'Không thể tải lên.',
                },
            ], $code === 'NOT_FOUND' ? 404 : 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu chứng từ chuyển khoản.',
            'chung_tu_ck_urls' => $result['chung_tu_ck_urls'],
        ]);
    }
}
