<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreDanhMucBaiHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug_danh_muc' => Str::slug((string) $this->input('ten_danh_muc')),
        ]);
    }

    public function rules(): array
    {
        return [
            'ten_danh_muc' => 'required|string|max:255',
            'slug_danh_muc' => 'required|string|max:255|unique:danh_muc_bai_hocs,slug_danh_muc',
            'mo_ta' => 'nullable|string',
            'thu_tu' => 'nullable|integer|min:1',
            'trang_thai' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'slug_danh_muc.unique' => 'Tên danh mục đã tồn tại hoặc bị trùng đường dẫn (slug).',
        ];
    }
}
