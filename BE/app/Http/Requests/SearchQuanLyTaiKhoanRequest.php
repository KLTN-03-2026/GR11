<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchQuanLyTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'nullable|string|max:255',
            'tu_khoa' => 'nullable|string|max:255',
        ];
    }
}
