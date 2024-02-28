<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zip_code_id' => 'required|exists:zip_codes,id',
            'education_level_id' => 'required|exists:education_levels,id',
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'work_experience' => 'nullable|string',
            'certifications' => 'nullable|string',
            'languages' => 'nullable|string',
            'references' => 'nullable|string',
            'expected_salary' => 'nullable|numeric|min:0',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Maximum file size: 2 MB
            'photo_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Maximum file size: 2 MB
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Maximum file size: 2 MB
            'social_networks' => 'nullable|json',
            'status' => 'required|in:Active,Blocked',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The user field does not exist.',
            'full_name.required' => 'The full name field is required.',
            'gender.nullable' => 'The gender field is optional.',
            'date_of_birth.nullable' => 'The date of birth field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'work_experience.nullable' => 'The work experience field is optional.',
            'skills.nullable' => 'The skills field is optional.',
            'certifications.nullable' => 'The certifications field is optional.',
            'languages.nullable' => 'The languages field is optional.',
            'references.nullable' => 'The references field is optional.',
            'expected_salary.nullable' => 'The expected salary field is optional.',
            'cv_file.nullable' => 'The cv file field is optional.',
            'photo_file.nullable' => 'The photo file field is optional.',
            'banner_file.nullable' => 'The banner file field is optional.',
            'social_networks.nullable' => 'The candidate social networks field is optional.',
            'status.required' => 'The status field is required.',

        ];
    }
}
