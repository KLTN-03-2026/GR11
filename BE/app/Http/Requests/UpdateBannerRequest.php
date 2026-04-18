<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
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
            'id' => 'required|integer|min:1',
            'image' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            // Thông báo lỗi cho ID
            'id.required' => 'Vui lòng cung cấp ID của banner cần cập nhật.',
            'id.integer'  => 'ID banner phải là một định dạng số.',
            'id.min'      => 'ID banner không hợp lệ (phải lớn hơn hoặc bằng 1).',

            // Thông báo lỗi cho Hình ảnh
            'image.string' => 'Đường dẫn hình ảnh phải là một chuỗi văn bản.',
            'image.max'    => 'Đường dẫn hình ảnh không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho Liên kết
            'link.string' => 'Đường dẫn liên kết phải là một chuỗi văn bản.',
            'link.max'    => 'Đường dẫn liên kết không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho Trạng thái
            'is_active.boolean' => 'Trạng thái hiển thị không hợp lệ (phải là true hoặc false).',
        ];
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
