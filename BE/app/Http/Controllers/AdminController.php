<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStatusQuanLyTaiKhoanRequest;
use App\Http\Requests\CreateQuanLyTaiKhoanRequest;
use App\Http\Requests\FilterByRoleQuanLyTaiKhoanRequest;
use App\Http\Requests\SearchQuanLyTaiKhoanRequest;
use App\Http\Requests\UpdateQuanLyTaiKhoanRequest;
use App\Models\NguoiDung;
use App\Models\VaiTro;
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
            'is_block' => $this->resolveIsBlock($request),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin đã tạo tài khoản ' . $nguoiDung->ho_ten . ' thành công ',
            'data' => $nguoiDung->load('vaiTro'),
        ]);
    }

    public function getdata()
    {
        $data = NguoiDung::with('vaiTro')->orderByDesc('id')->get();

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
            'is_block' => $this->resolveIsBlock($request, (int) $nguoiDung->is_block),
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
            'data' => $nguoiDung->fresh()->load('vaiTro'),
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

        $nguoiDung->is_block = (int) !$nguoiDung->is_block;
        $nguoiDung->save();

        return response()->json([
            'status' => true,
            'message' => 'Admin đã đổi trạng thái ' . $nguoiDung->ho_ten . ' thành công ',
            'data' => $nguoiDung,
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
            ->get();

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
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
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

    private function resolveIsBlock($request, ?int $default = null): int
    {
        if ($request->has('is_block')) {
            return (int) $request->boolean('is_block');
        }

        if ($request->has('trang_thai')) {
            $isActive = (int) $request->input('trang_thai') === 1;
            return $isActive ? 0 : 1;
        }

        if ($request->has('isActive')) {
            return $request->boolean('isActive') ? 0 : 1;
        }

        return $default ?? 0;
    }
}
