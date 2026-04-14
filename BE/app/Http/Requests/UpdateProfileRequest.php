<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_va_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'nullable|string|max:15',
            'dia_chi' => 'nullable|string|max:255',
        ];
    }
}
