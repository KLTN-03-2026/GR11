<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetBaiHocTheoDanhMucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:danh_muc_bai_hocs,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID danh mục.',
            'id.exists' => 'Danh mục không tồn tại.',
        ];
    }
}
