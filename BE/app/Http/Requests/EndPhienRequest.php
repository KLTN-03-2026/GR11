<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndPhienRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'phien_id' => 'required|integer|exists:phien_luyen_taps,id',
        ];
    }

    public function messages(): array
    {
        return [
            'phien_id.required' => 'Vui lòng cung cấp phien_id.',
            'phien_id.integer' => 'phien_id không hợp lệ.',
            'phien_id.exists' => 'Phiên luyện tập không tồn tại.',
        ];
    }
}
