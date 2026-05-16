<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ResolvesTeacherMediaUrl;
use App\Http\Requests\DestroyTeacherTuVungRequest;
use App\Http\Requests\ImportTeacherTuVungExcelRequest;
use App\Http\Requests\ListTeacherTuVungRequest;
use App\Http\Requests\StoreTeacherTuVungRequest;
use App\Http\Requests\UpdateTeacherTuVungRequest;
use App\Http\Requests\UploadTeacherTuVungMediaRequest;
use App\Models\BaiHoc;
use App\Models\ChiTietLuyenTap;
use App\Models\TuVung;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TeacherTuVungController extends Controller
{
    use ResolvesTeacherMediaUrl;

    public function index(ListTeacherTuVungRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::query()->find($id);
        if (! $baiHoc) {
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
        if (! $baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học.',
            ], 404);
        }

        $tuVung = DB::transaction(fn () => $this->insertTuVungRow(
            $id,
            $request->tu_chuan,
            $request->phien_am,
            $request->cap_do,
            $request->hinh_anh_url,
            $request->am_thanh_mau_url,
            $request->input('thu_tu'),
        ));

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm từ vựng thành công.',
            'data' => $this->mapTuVungRow($tuVung),
        ], 201);
    }

    public function uploadMedia(UploadTeacherTuVungMediaRequest $request, int $id): JsonResponse
    {
        if (! function_exists('cloudinary')) {
            return response()->json([
                'status' => false,
                'message' => 'Upload chưa được cấu hình trên máy chủ (Cloudinary).',
            ], 503);
        }

        $file = $request->file('file');
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return response()->json([
                'status' => false,
                'message' => 'Tệp tải lên không hợp lệ.',
            ], 422);
        }

        $kind = $request->input('kind');
        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => $kind === 'image'
                    ? ['max:6144', 'mimes:jpeg,jpg,png,gif,webp']
                    : ['max:15360', 'mimes:mp3,mpeg,wav,ogg,m4a'],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $kind === 'image'
                    ? 'Ảnh không hợp lệ (JPEG/PNG/GIF/WebP, tối đa ~6 MB).'
                    : 'Âm thanh không hợp lệ (MP3/WAV/OGG/M4A, tối đa ~15 MB).',
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $path = $file->getRealPath();
        if ($path === false) {
            return response()->json([
                'status' => false,
                'message' => 'Không đọc được tệp tạm.',
            ], 422);
        }

        try {
            if ($kind === 'image') {
                $uploaded = cloudinary()->uploadApi()->upload($path, [
                    'folder' => 'teacher-tu-vung/'.$id.'/images',
                ]);
            } else {
                $uploaded = cloudinary()->uploadApi()->upload($path, [
                    'folder' => 'teacher-tu-vung/'.$id.'/audio',
                    'resource_type' => 'raw',
                ]);
            }

            $url = (string) ($uploaded['secure_url'] ?? '');
            if ($url === '') {
                return response()->json([
                    'status' => false,
                    'message' => 'Dịch vụ lưu trữ không trả về địa chỉ file.',
                ], 502);
            }

            if (strlen($url) > 255) {
                return response()->json([
                    'status' => false,
                    'message' => 'URL file vượt quá 255 ký tự (giới hạn cơ sở dữ liệu). Vui lòng liên hệ quản trị để nới cột.',
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => 'Đã tải file lên Cloudinary.',
                'data' => [
                    'url' => $url,
                    'kind' => $kind,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Không tải lên được. Kiểm tra cấu hình Cloudinary hoặc thử lại sau.',
            ], 502);
        }
    }

    public function importFromExcel(ImportTeacherTuVungExcelRequest $request, int $id): JsonResponse
    {
        $baiHoc = BaiHoc::query()->find($id);
        if (! $baiHoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài học.',
            ], 404);
        }

        try {
            $path = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $rows = $spreadsheet->getActiveSheet()->toArray();
        } catch (Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Không đọc được file. Vui lòng kiểm tra định dạng .xlsx / .xls / .csv.',
            ], 422);
        }

        if ($rows === [] || ! isset($rows[0])) {
            return response()->json([
                'status' => false,
                'message' => 'File không có dữ liệu.',
            ], 422);
        }

        $colMap = $this->mapTuVungExcelHeaderRow($rows[0]);
        if (! isset($colMap['tu_chuan'])) {
            return response()->json([
                'status' => false,
                'message' => 'Thiếu cột bắt buộc «Từ chuẩn» (hoặc tu_chuan) ở hàng đầu tiên.',
            ], 422);
        }

        $existingWords = TuVung::query()
            ->where('bai_hoc_id', $id)
            ->pluck('tu_chuan')
            ->all();
        $existingMap = [];
        foreach ($existingWords as $word) {
            $normalized = $this->normalizeTuChuanForCompare((string) $word);
            if ($normalized !== '') {
                $existingMap[$normalized] = true;
            }
        }

        $parsed = [];
        $rowErrors = [];
        $skippedExisting = [];

        for ($i = 1, $n = count($rows); $i < $n; $i++) {
            $line = $i + 1;
            $row = $rows[$i];
            $tuChuan = $this->excelCellString($row, $colMap['tu_chuan'] ?? null);
            if ($tuChuan === '') {
                continue;
            }

            if (mb_strlen($tuChuan) > 100) {
                $rowErrors[] = "Dòng {$line}: «Từ chuẩn» vượt quá 100 ký tự.";

                continue;
            }

            $phienAm = $this->excelCellString($row, $colMap['phien_am'] ?? null);
            if ($phienAm !== '' && mb_strlen($phienAm) > 100) {
                $rowErrors[] = "Dòng {$line}: «Phiên âm» vượt quá 100 ký tự.";

                continue;
            }

            $capRaw = $this->excelCellString($row, $colMap['cap_do'] ?? null);
            $capDo = $this->normalizeCapDoFromExcel($capRaw);
            if ($capDo === null) {
                $rowErrors[] = "Dòng {$line}: Cấp độ không hợp lệ (dùng: dễ, trung bình, khó hoặc de, trung_binh, kho).";

                continue;
            }

            $hinh = $this->excelCellString($row, $colMap['hinh_anh_url'] ?? null);
            $am = $this->excelCellString($row, $colMap['am_thanh_mau_url'] ?? null);
            if ($hinh !== '' && strlen($hinh) > 255) {
                $rowErrors[] = "Dòng {$line}: URL hình ảnh quá dài.";

                continue;
            }
            if ($am !== '' && strlen($am) > 255) {
                $rowErrors[] = "Dòng {$line}: URL âm thanh quá dài.";

                continue;
            }

            $thuTuVal = null;
            if (isset($colMap['thu_tu'])) {
                $thuTuRaw = $row[$colMap['thu_tu']] ?? null;
                if ($thuTuRaw !== null && $thuTuRaw !== '') {
                    if (! is_numeric($thuTuRaw) || (int) $thuTuRaw < 1) {
                        $rowErrors[] = "Dòng {$line}: «Thứ tự» phải là số nguyên ≥ 1.";

                        continue;
                    }
                    $thuTuVal = (int) $thuTuRaw;
                }
            }

            $normalizedTuChuan = $this->normalizeTuChuanForCompare($tuChuan);
            if ($normalizedTuChuan !== '' && isset($existingMap[$normalizedTuChuan])) {
                $skippedExisting[] = [
                    'line' => $line,
                    'tu_chuan' => $tuChuan,
                ];

                continue;
            }

            $parsed[] = [
                'line' => $line,
                'tu_chuan' => $tuChuan,
                'phien_am' => $phienAm === '' ? null : $phienAm,
                'cap_do' => $capDo,
                'hinh_anh_url' => $hinh === '' ? null : $hinh,
                'am_thanh_mau_url' => $am === '' ? null : $am,
                'thu_tu' => $thuTuVal,
            ];
        }

        if ($parsed === []) {
            if ($skippedExisting !== [] && $rowErrors === []) {
                return response()->json([
                    'status' => true,
                    'message' => 'Không có từ mới để thêm. Tất cả từ trong file đã tồn tại.',
                    'data' => [
                        'imported' => 0,
                        'skipped_existing' => $skippedExisting,
                    ],
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $rowErrors === []
                    ? 'Không có dòng dữ liệu hợp lệ (cần ít nhất một ô «Từ chuẩn»).'
                    : 'Không import được dòng nào.',
                'errors' => $rowErrors,
            ], 422);
        }

        if ($rowErrors !== []) {
            return response()->json([
                'status' => false,
                'message' => 'File có lỗi. Sửa các dòng báo lỗi rồi thử lại.',
                'errors' => $rowErrors,
            ], 422);
        }

        $imported = 0;
        DB::transaction(function () use ($parsed, $id, &$imported): void {
            foreach ($parsed as $item) {
                $this->insertTuVungRow(
                    $id,
                    $item['tu_chuan'],
                    $item['phien_am'],
                    $item['cap_do'],
                    $item['hinh_anh_url'],
                    $item['am_thanh_mau_url'],
                    $item['thu_tu'],
                );
                $imported++;
            }
        });

        $message = "Đã nhập {$imported} từ vựng từ file.";
        if ($skippedExisting !== []) {
            $message .= ' Bỏ qua '.count($skippedExisting).' từ đã có sẵn.';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'imported' => $imported,
                'skipped_existing' => $skippedExisting,
            ],
        ], 201);
    }

    public function update(UpdateTeacherTuVungRequest $request, int $id): JsonResponse
    {
        $tuVung = TuVung::query()->find($id);
        if (! $tuVung) {
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
        if (! $tuVung) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy từ vựng.',
            ], 404);
        }

        try {
            DB::transaction(function () use ($tuVung): void {
                ChiTietLuyenTap::query()->where('tu_vung_id', $tuVung->id)->delete();
                $tuVung->delete();
            });
        } catch (QueryException) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa từ vựng vì vẫn còn dữ liệu liên quan trên hệ thống. Liên hệ quản trị hoặc chạy cập nhật cơ sở dữ liệu (migration) mới nhất.',
            ], 409);
        }

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
            'hinh_anh_url' => $this->resolveTeacherMediaUrl($tv->hinh_anh_url),
            'am_thanh_mau_url' => $this->resolveTeacherMediaUrl($tv->am_thanh_mau_url),
            'thu_tu' => $tv->thu_tu,
        ];
    }

    private function insertTuVungRow(
        int $baiHocId,
        string $tuChuan,
        ?string $phienAm,
        string $capDo,
        ?string $hinhAnhUrl,
        ?string $amThanhUrl,
        mixed $thuTuInput,
    ): TuVung {
        $thuTu = $thuTuInput;
        if ($thuTu === null || $thuTu === '') {
            $max = TuVung::query()->where('bai_hoc_id', $baiHocId)->max('thu_tu');
            $thuTu = $max === null ? 1 : (int) $max + 1;
        } else {
            $thuTu = (int) $thuTu;
            TuVung::query()
                ->where('bai_hoc_id', $baiHocId)
                ->where('thu_tu', '>=', $thuTu)
                ->update(['thu_tu' => DB::raw('thu_tu + 1')]);
        }

        return TuVung::query()->create([
            'bai_hoc_id' => $baiHocId,
            'tu_chuan' => $tuChuan,
            'phien_am' => $phienAm,
            'cap_do' => $capDo,
            'hinh_anh_url' => $hinhAnhUrl,
            'am_thanh_mau_url' => $amThanhUrl,
            'thu_tu' => $thuTu,
        ]);
    }

    /**
     * @param  array<int, mixed>  $headerRow
     * @return array<string, int>
     */
    private function mapTuVungExcelHeaderRow(array $headerRow): array
    {
        $aliases = [
            'tu_chuan' => ['tu_chuan', 'từ chuẩn', 'tu chuan', 'từ', 'word', 'từ vựng', 'từvựng'],
            'phien_am' => ['phien_am', 'phiên âm', 'phien am', 'hướng dẫn'],
            'cap_do' => ['cap_do', 'cấp độ', 'cap do', 'mức độ', 'độ khó'],
            'hinh_anh_url' => ['hinh_anh_url', 'hình ảnh', 'hinh anh', 'url ảnh', 'ảnh', 'link ảnh'],
            'am_thanh_mau_url' => ['am_thanh_mau_url', 'âm thanh', 'am thanh', 'url âm thanh', 'mp3', 'link âm thanh'],
            'thu_tu' => ['thu_tu', 'thứ tự', 'thu tu', 'stt', 'tt', 'số thứ tự'],
        ];

        $lookup = [];
        foreach ($aliases as $key => $names) {
            foreach ($names as $name) {
                $lookup[mb_strtolower(trim($name), 'UTF-8')] = $key;
            }
        }

        $map = [];
        foreach ($headerRow as $colIdx => $cell) {
            $h = $this->normalizeExcelHeaderCell($cell);
            if ($h === '' || ! isset($lookup[$h])) {
                continue;
            }
            $logical = $lookup[$h];
            if (isset($map[$logical])) {
                continue;
            }
            $map[$logical] = (int) $colIdx;
        }

        return $map;
    }

    private function normalizeExcelHeaderCell(mixed $cell): string
    {
        $s = trim((string) $cell);
        $s = preg_replace('/^\x{FEFF}/u', '', $s) ?? $s;

        return mb_strtolower(trim($s), 'UTF-8');
    }

    /**
     * @param  array<int, mixed>|null  $row
     */
    private function excelCellString(?array $row, ?int $colIdx): string
    {
        if ($row === null || $colIdx === null || ! array_key_exists($colIdx, $row)) {
            return '';
        }
        $v = $row[$colIdx];
        if ($v === null) {
            return '';
        }

        return trim((string) $v);
    }

    private function normalizeCapDoFromExcel(string $raw): ?string
    {
        if (trim($raw) === '') {
            return 'de';
        }
        $v = mb_strtolower(trim($raw), 'UTF-8');
        $c = str_replace([' ', '_', '-'], '', $v);

        if (in_array($v, ['de', 'dễ', 'basic', 'dê'], true) || in_array($c, ['de', 'dễ', 'basic'], true)) {
            return 'de';
        }
        if (in_array($v, ['trung_binh', 'trung bình', 'tb'], true) || in_array($c, ['trungbình', 'trungbinh'], true)) {
            return 'trung_binh';
        }
        if (in_array($v, ['kho', 'khó'], true) || $c === 'khó' || $c === 'kho') {
            return 'kho';
        }

        return null;
    }

    private function normalizeTuChuanForCompare(string $word): string
    {
        return mb_strtolower(trim($word), 'UTF-8');
    }
}
