<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadTeacherTuVungMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bai_hocs,id',
            'kind' => ['required', 'string', Rule::in(['image', 'audio'])],
            'file' => ['required', 'file'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.exists' => 'Bài học không tồn tại.',
            'kind.in' => 'Loại tệp phải là image hoặc audio.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
