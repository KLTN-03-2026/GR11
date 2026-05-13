<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherBaiHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'danh_muc_id' => 'required|integer|exists:danh_muc_bai_hocs,id',
            'tieu_de' => 'required|string|max:200',
            'mo_ta' => 'nullable|string',
            'cap_do' => 'required|string|in:de,trung_binh,kho',
            'thu_tu' => 'nullable|integer|min:1',
            'trang_thai' => 'nullable|integer|in:0,1,2',
        ];
    }

    public function messages(): array
    {
        return [
            'danh_muc_id.required' => 'Vui lòng chọn danh mục.',
            'danh_muc_id.exists' => 'Danh mục không tồn tại.',
            'tieu_de.required' => 'Vui lòng nhập tiêu đề bài học.',
            'cap_do.required' => 'Vui lòng chọn cấp độ.',
            'cap_do.in' => 'Cấp độ không hợp lệ.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
