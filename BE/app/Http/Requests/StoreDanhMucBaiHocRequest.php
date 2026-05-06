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
        $slugInput = trim((string) $this->input('slug_danh_muc', ''));
        $tenInput = trim((string) $this->input('ten_danh_muc', ''));

        $this->merge([
            'slug_danh_muc' => $slugInput !== '' ? Str::slug($slugInput) : Str::slug($tenInput),
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
            'slug_danh_muc.required' => 'Vui lòng nhập slug danh mục.',
            'slug_danh_muc.unique' => 'Slug danh mục đã tồn tại, vui lòng nhập slug khác.',
        ];
    }
}
