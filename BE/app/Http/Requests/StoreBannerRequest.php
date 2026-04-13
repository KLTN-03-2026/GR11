<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'required|string|max:255',
            'link' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            // Thông báo lỗi cho trường 'image'
            'image.required' => 'Vui lòng cung cấp đường dẫn hình ảnh cho banner.',
            'image.string'   => 'Đường dẫn hình ảnh phải là một chuỗi văn bản.',
            'image.max'      => 'Đường dẫn hình ảnh không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho trường 'link'
            'link.string'    => 'Đường dẫn liên kết phải là một chuỗi văn bản.',
            'link.max'       => 'Đường dẫn liên kết không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho trường 'is_active'
            'is_active.required' => 'Vui lòng xác định trạng thái hiển thị (Bật/Tắt) của banner.',
            'is_active.boolean'  => 'Trạng thái hiển thị không hợp lệ (phải là true hoặc false).',
        ];
    }
}
