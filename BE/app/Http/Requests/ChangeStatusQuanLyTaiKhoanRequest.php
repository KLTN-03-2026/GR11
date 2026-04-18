<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusQuanLyTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:nguoi_dungs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID tài khoản cần đổi trạng thái.',
            'id.exists' => 'Tài khoản cần đổi trạng thái không tồn tại.',
        ];
    }
}
