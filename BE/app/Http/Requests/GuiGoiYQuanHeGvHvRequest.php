<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuiGoiYQuanHeGvHvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hoc_vien_id' => 'required|integer|exists:nguoi_dungs,id',
            'bai_hoc_id' => 'required|integer|exists:bai_hocs,id',
            'uu_tien' => 'required|string|in:cao,binh_thuong',
            'loi_nhan' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'hoc_vien_id.required' => 'Vui lòng chọn học viên.',
            'hoc_vien_id.exists' => 'Học viên không tồn tại.',
            'bai_hoc_id.required' => 'Vui lòng chọn bài học.',
            'bai_hoc_id.exists' => 'Bài học không tồn tại.',
            'uu_tien.required' => 'Vui lòng chọn mức độ ưu tiên.',
            'uu_tien.in' => 'Mức độ ưu tiên không hợp lệ.',
        ];
    }
}
