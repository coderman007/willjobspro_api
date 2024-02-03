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
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'work_experience' => 'nullable|string',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
            'certifications' => 'nullable|string',
            'languages' => 'nullable|string',
            'references' => 'nullable|string',
            'expected_salary' => 'nullable|numeric|min:0',
            'cv_path' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'banner_path' => 'nullable|string|max:255',
            'candidate_social_networks' => 'nullable|json',
            'status' => 'required|in:Active,Inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The user field does not exist.',
            'full_name.required' => 'The full name field is required.',
            'gender.required' => 'The gender field is required.',
            'date_of_birth.nullable' => 'The date of birth field is optional.',
            'address.nullable' => 'The address field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'work_experience.nullable' => 'The work experience field is optional.',
            'education.nullable' => 'The education field is optional.',
            'skills.nullable' => 'The skills field is optional.',
            'certifications.nullable' => 'The certifications field is optional.',
            'languages.nullable' => 'The languages field is optional.',
            'references.nullable' => 'The references field is optional.',
            'expected_salary.nullable' => 'The expected salary field is optional.',
            'cv_path.nullable' => 'The cv path field is optional.',
            'photo_path.nullable' => 'The photo path field is optional.',
            'banner_path.nullable' => 'The banner path field is optional.',
            'candidate_social_networks.nullable' => 'The candidate social networks field is optional.',
            'status.required' => 'The status field is required.',

        ];
    }
}
