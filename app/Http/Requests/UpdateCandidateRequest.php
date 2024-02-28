<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'education_level_id' => 'exists:education_levels,id',
            'country_id' => 'exists:countries,id',
            'state_id' => 'exists:states,id',
            'city_id' => 'exists:cities,id',
            'zip_code_id' => 'exists:zip_codes,id',
            'full_name' => 'string|max:255',
            'gender' => 'string|max:20',
            'date_of_birth' => 'date',
            'phone_number' => 'string|max:20',
            'work_experience' => 'string',
            'certifications' => 'string',
            'languages' => 'string',
            'references' => 'string',
            'expected_salary' => 'numeric|min:0',
            'cv_path' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'banner_path' => 'nullable|string|max:255',
            'social_networks' => 'nullable|json',
            'status' => 'in:Active,Blocked',
        ];
    }
}
