<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuanLyTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten' => 'required_without:name|string|max:100',
            'name' => 'required_without:ho_ten|string|max:100',
            'email' => 'required|email|max:150|unique:nguoi_dungs,email',
            'mat_khau' => 'required_without:password|string|min:6',
            'password' => 'required_without:mat_khau|string|min:6',
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
            'ho_ten.required_without' => 'Vui lòng nhập họ tên.',
            'name.required_without' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required_without' => 'Vui lòng nhập mật khẩu.',
            'password.required_without' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min' => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
            'vai_tro_id.exists' => 'Vai trò không tồn tại.',
        ];
    }
}
