<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBannerRequest extends FormRequest
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
        ];
    }
    public function messages(): array
    {
        return [
            'id.required' => 'Vui lòng cung cấp ID của banner cần xóa.',
            'id.integer'  => 'ID banner phải là một định dạng số.',
            'id.min'      => 'ID banner không hợp lệ (phải lớn hơn hoặc bằng 1).',
        ];
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
