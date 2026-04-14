<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuanLyTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'id' => 'required|integer|exists:nguoi_dungs,id',
            'ho_ten' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150|unique:nguoi_dungs,email,' . $id,
            'mat_khau' => 'nullable|string|min:6',
            'password' => 'nullable|string|min:6',
            'sdt' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:10',
            'vai_tro_id' => 'nullable|integer|exists:vai_tros,id',
            'role' => 'nullable|string|max:50',
            'trang_thai' => 'nullable|boolean',
            'isActive' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID tài khoản cần cập nhật.',
            'id.exists' => 'Tài khoản cần cập nhật không tồn tại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.min' => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
            'vai_tro_id.exists' => 'Vai trò không tồn tại.',
        ];
    }
}
