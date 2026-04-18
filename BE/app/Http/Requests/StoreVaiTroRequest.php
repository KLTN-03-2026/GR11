<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVaiTroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_vai_tro' => 'required|string|max:50|unique:vai_tros,ten_vai_tro',
            'mo_ta' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_vai_tro.required' => 'Vui lòng nhập tên vai trò.',
            'ten_vai_tro.unique' => 'Tên vai trò đã tồn tại.',
        ];
    }
}
