<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $phone = (string) $this->input('so_dien_thoai', '');
        $phone = preg_replace('/\s+/', '', $phone) ?? $phone;

        $this->merge([
            'so_dien_thoai' => $phone,
        ]);
    }

    public function rules(): array
    {
        return [
            'ho_va_ten' => 'required|string|min:2|max:100',
            'so_dien_thoai' => ['nullable', 'regex:/^(0|\+84)[0-9]{8,10}$/'],
            'dia_chi' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_va_ten.required' => 'Vui lòng nhập họ và tên.',
            'ho_va_ten.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ. Vui lòng nhập đúng định dạng Việt Nam.',
        ];
    }
}
