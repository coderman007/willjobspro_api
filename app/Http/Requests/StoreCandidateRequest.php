<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('candidate');
    }

    public function rules(): array
    {
        return [
            'gender' => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'expected_salary' => 'nullable|numeric',
            'status' => 'nullable|in:Active,Blocked',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zip_code_id' => 'nullable|exists:zip_codes,id',
            'skills' => 'nullable|string',
            'languages' => 'nullable|string',
            'social_networks' => 'nullable|string',
            'education_levels' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Ejemplo: PDF, DOC, DOCX con un tamaño máximo de 2 MB
            'photo_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
        ];
    }

    public function messages(): array
    {
        return [
            'gender.nullable' => 'The gender field is optional.',
            'date_of_birth.nullable' => 'The date of birth field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'expected_salary.nullable' => 'The expected salary field is optional.',
            'status.nullable' => 'The status field is optional.',
            'country_id.nullable' => 'The country id field is optional.',
            'state_id.nullable' => 'The state id field is optional.',
            'city_id.nullable' => 'The city id field is optional.',
            'zip_code_id.nullable' => 'The zip code id field is optional.',
            'skills.nullable' => 'The skills field is optional.',
            'education_levels.nullable' => 'The education levels field is optional.',
            'languages.nullable' => 'The languages field is optional.',
            'social_networks.nullable' => 'The social networks field is optional.',
            'cv_file.nullable' => 'The cv file field is optional.',
            'photo_file.nullable' => 'The photo file field is optional.',
            'banner_file.nullable' => 'The banner file field is optional.',
        ];
    }
}
