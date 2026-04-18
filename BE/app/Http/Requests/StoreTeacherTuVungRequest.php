<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherTuVungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tu_chuan' => 'required|string|max:100',
            'phien_am' => 'nullable|string|max:100',
            'cap_do' => 'required|string|in:de,trung_binh,kho',
            'hinh_anh_url' => 'nullable|string|max:255',
            'am_thanh_mau_url' => 'nullable|string|max:255',
            'thu_tu' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tu_chuan.required' => 'Vui lòng nhập từ chuẩn.',
            'cap_do.required' => 'Vui lòng chọn cấp độ.',
            'cap_do.in' => 'Cấp độ không hợp lệ.',
        ];
    }
}
