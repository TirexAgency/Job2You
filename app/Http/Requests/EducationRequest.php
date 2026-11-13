<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ComposesDateFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
{
    use ComposesDateFields;

    protected function prepareForValidation(): void
    {
        $this->composeDateFields(['start_date', 'end_date']);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school' => ['required', 'string', 'max:150'],
            'degree' => ['required', 'string', 'max:150'],
            'field' => ['nullable', 'string', 'max:150'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
