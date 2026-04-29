<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartPhienRequest extends FormRequest
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
            'bai_hoc_id' => 'required|integer|exists:bai_hocs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'bai_hoc_id.required' => 'Vui lòng cung cấp bai_hoc_id.',
            'bai_hoc_id.integer' => 'bai_hoc_id không hợp lệ.',
            'bai_hoc_id.exists' => 'Bài học không tồn tại.',
        ];
    }
}
