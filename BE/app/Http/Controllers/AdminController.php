<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStatusQuanLyTaiKhoanRequest;
use App\Http\Requests\CreateQuanLyTaiKhoanRequest;
use App\Http\Requests\FilterByRoleQuanLyTaiKhoanRequest;
use App\Http\Requests\SearchQuanLyTaiKhoanRequest;
use App\Http\Requests\UpdateQuanLyTaiKhoanRequest;
use App\Models\NguoiDung;
use App\Models\VaiTro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function store(CreateQuanLyTaiKhoanRequest $request)
    {
        $vaiTroId = $this->resolveVaiTroId($request->input('vai_tro_id', $request->input('role')));

        $nguoiDung = NguoiDung::create([
            'ho_ten' => $request->input('ho_ten', $request->input('name')),
            'email' => $request->input('email'),
            'mat_khau' => Hash::make($request->input('mat_khau', $request->input('password'))),
            'sdt' => $request->input('sdt', $request->input('phone')),
            'vai_tro_id' => $vaiTroId ?? NguoiDung::ROLE_USER,
            'trang_thai' => $this->resolveTrangThai($request),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin đã tạo tài khoản ' . $nguoiDung->ho_ten . ' thành công ',
            'data' => $this->formatUser($nguoiDung->load('vaiTro')),
        ]);
    }

    public function getdata()
    {
        $data = NguoiDung::with('vaiTro')->orderByDesc('id')->get()->map(fn($u) => $this->formatUser($u));

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function update(UpdateQuanLyTaiKhoanRequest $request)
    {
        $nguoiDung = NguoiDung::find($request->id);
        if (!$nguoiDung) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tài khoản cần cập nhật',
            ], 404);
        }

        $vaiTroId = $this->resolveVaiTroId($request->input('vai_tro_id', $request->input('role')));

        $dataUpdate = [
            'ho_ten' => $request->input('ho_ten', $request->input('name', $nguoiDung->ho_ten)),
            'email' => $request->input('email', $nguoiDung->email),
            'sdt' => $request->input('sdt', $request->input('phone', $nguoiDung->sdt)),
            'trang_thai' => $this->resolveTrangThai($request, (int) $nguoiDung->trang_thai),
        ];

        if ($vaiTroId !== null) {
            $dataUpdate['vai_tro_id'] = $vaiTroId;
        }

        $matKhauMoi = $request->input('mat_khau', $request->input('password'));
        if (!empty($matKhauMoi)) {
            $dataUpdate['mat_khau'] = Hash::make($matKhauMoi);
        }

        $nguoiDung->update($dataUpdate);

        return response()->json([
            'status' => true,
            'message' => 'Admin đã cập nhật tài khoản ' . $nguoiDung->ho_ten . ' thành công ',
            'data' => $this->formatUser($nguoiDung->fresh()->load('vaiTro')),
        ]);
    }

    public function changeStatus(ChangeStatusQuanLyTaiKhoanRequest $request)
    {
        $nguoiDung = NguoiDung::find($request->id);
        if (!$nguoiDung) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tài khoản cần đổi trạng thái',
            ], 404);
        }

        $current = Auth::guard('sanctum')->user();
        if (!$current) {
            return response()->json([
                'status' => false,
                'message' => 'Phiên đăng nhập không hợp lệ.',
            ], 401);
        }

        $dangHoatDong = ((int) $nguoiDung->trang_thai === 0);

        if ($dangHoatDong && (int) $nguoiDung->id === (int) $current->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không thể khóa tài khoản đang đăng nhập.',
            ], 422);
        }

        if ($dangHoatDong) {
            $nguoiDung->trang_thai = 1;
            $nguoiDung->content_block = (string) $request->input('content_block', '');
        } else {
            $nguoiDung->trang_thai = 0;
            $nguoiDung->content_block = null;
        }

        $nguoiDung->save();

        if ((int) $nguoiDung->trang_thai === 1) {
            $nguoiDung->tokens()->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Admin đã đổi trạng thái ' . $nguoiDung->ho_ten . ' thành công ',
            'data' => $this->formatUser($nguoiDung->fresh()->load('vaiTro')),
        ]);
    }

    public function search(SearchQuanLyTaiKhoanRequest $request)
    {
        $key = $request->input('key', $request->input('tu_khoa', ''));

        $data = NguoiDung::with('vaiTro')
            ->when($key !== '', function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->where('ho_ten', 'like', '%' . $key . '%')
                        ->orWhere('email', 'like', '%' . $key . '%')
                        ->orWhere('sdt', 'like', '%' . $key . '%');
                });
            })
            ->orderByDesc('id')
            ->get()
            ->map(fn($u) => $this->formatUser($u));

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function filterByRole(FilterByRoleQuanLyTaiKhoanRequest $request)
    {
        $vaiTroId = $this->resolveVaiTroId($request->input('vai_tro_id', $request->input('role')));

        $data = NguoiDung::with('vaiTro')
            ->when($vaiTroId !== null, function ($query) use ($vaiTroId) {
                $query->where('vai_tro_id', $vaiTroId);
            })
            ->orderByDesc('id')
            ->get()
            ->map(fn($u) => $this->formatUser($u));

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function export(Request $request)
    {
        $key = $request->query('key', '');
        $vaiTroId = $this->resolveVaiTroId($request->query('vai_tro_id'));
        $isBlock = $request->query('is_block');

        $users = NguoiDung::with('vaiTro')
            ->when($key !== '', function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->where('ho_ten', 'like', '%' . $key . '%')
                        ->orWhere('email', 'like', '%' . $key . '%')
                        ->orWhere('sdt', 'like', '%' . $key . '%');
                });
            })
            ->when($vaiTroId !== null, function ($query) use ($vaiTroId) {
                $query->where('vai_tro_id', $vaiTroId);
            })
            ->when($isBlock !== null && $isBlock !== '', function ($query) use ($isBlock) {
                $query->where('trang_thai', (int) $isBlock);
            })
            ->orderByDesc('id')
            ->get();

        $rows = [
            ['Tên', 'Email', 'SĐT', 'Vai trò', 'Trạng thái', 'Ngày tạo'],
        ];

        foreach ($users as $user) {
            $status = ((int) ($user->trang_thai ?? 0) === 1) ? 'Tạm khóa' : 'Đang hoạt động';
            $rows[] = [
                $user->ho_ten,
                $user->email,
                $user->sdt ?? '',
                $user->vaiTro?->ten_vai_tro ?? ($user->vai_tro_id === NguoiDung::ROLE_ADMIN ? 'Admin' : ($user->vai_tro_id === NguoiDung::ROLE_TEACHER ? 'Giáo viên' : 'Học viên')),
                $status,
                $user->ngay_tao ? Carbon::parse($user->ngay_tao)->format('d/m/Y') : '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $escaped = array_map(static function ($value): string {
                $text = (string) ($value ?? '');
                $text = str_replace('"', '""', $text);
                return '"' . $text . '"';
            }, $row);
            $csv .= implode(',', $escaped) . "\r\n";
        }

        $csv = "\xFF\xFE" . mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
        $filename = 'quan-ly-tai-khoan-' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function resolveVaiTroId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $normalized = mb_strtolower(trim((string) $value));
        $map = [
            'admin' => NguoiDung::ROLE_ADMIN,
            'giáo viên' => NguoiDung::ROLE_TEACHER,
            'giao vien' => NguoiDung::ROLE_TEACHER,
            'học viên' => NguoiDung::ROLE_USER,
            'hoc vien' => NguoiDung::ROLE_USER,
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        $vaiTro = VaiTro::whereRaw('LOWER(ten_vai_tro) = ?', [$normalized])->first();
        return $vaiTro?->id;
    }

    private function resolveTrangThai($request, ?int $default = null): int
    {
        if ($request->has('is_block')) {
            return $request->boolean('is_block') ? 1 : 0;
        }

        if ($request->has('trang_thai')) {
            return ((int) $request->input('trang_thai') === 1) ? 1 : 0;
        }

        if ($request->has('isActive')) {
            return $request->boolean('isActive') ? 0 : 1;
        }

        return ($default === 1) ? 1 : 0;
    }

    private function formatUser(NguoiDung $user): NguoiDung
    {
        $user->setAttribute('is_block', (int) ($user->trang_thai ?? 0) === 1 ? 1 : 0);
        return $user;
    }
}
