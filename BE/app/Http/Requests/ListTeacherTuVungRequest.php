<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTeacherTuVungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bai_hocs,id',
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
            'id.exists' => 'Bài học không tồn tại.',
        ];
    }
}
