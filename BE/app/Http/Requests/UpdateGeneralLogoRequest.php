<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Vui lòng chọn logo cần tải lên.',
            'logo.image' => 'Logo phải là file ảnh hợp lệ.',
            'logo.mimes' => 'Logo chỉ hỗ trợ định dạng jpg, jpeg, png hoặc webp.',
            'logo.max' => 'Logo không được vượt quá 2MB.',
        ];
    }
}
