<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterByRoleQuanLyTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vai_tro_id' => 'nullable|integer|exists:vai_tros,id',
            'role' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'vai_tro_id.exists' => 'Vai trò không tồn tại.',
        ];
    }
}
