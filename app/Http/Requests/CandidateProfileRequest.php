<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CandidateProfileRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'desired_jobs' => ['nullable', 'string', 'max:50'],
            'experience_level' => ['required', Rule::in(['junior', 'mid', 'senior'])],
            'education_level' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:150'],
            'region' => ['nullable', 'string', 'max:150'],
            'mobility' => ['required', Rule::in(['local', 'regional', 'national', 'remote', 'international'])],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'contract_type' => ['nullable', Rule::in(['cdi', 'cdd', 'freelance', 'stage', 'alternance', 'interim'])],
            'desired_salary' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'sectors_text' => ['nullable', 'string', 'max:2000'],
            'alerts_enabled' => ['boolean'],
        ];
    }
}
