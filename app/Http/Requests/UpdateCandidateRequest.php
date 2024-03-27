<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
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
            'work_experience' => 'nullable|string',
            'certifications' => 'nullable|string',
            'references' => 'nullable|string',
            'expected_salary' => 'nullable|numeric',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Ejemplo: PDF, DOC, DOCX con un tamaño máximo de 2 MB
            'photo_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'social_networks' => 'nullable|json',
            'status' => 'nullable|in:Active,Blocked',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zip_code_id' => 'nullable|exists:zip_codes,id',
            'skills' => 'nullable|string',
            'languages' => 'nullable|string',
            'education_levels' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'gender.string' => 'The gender must be a string.',
            'gender.in' => 'The gender must be one of: Male, Female, Other.',
            'date_of_birth.date' => 'The date of birth must be a valid date.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number may not be greater than :max characters.',
            'cv_path.file' => 'The cv file must be a file.',
            'cv_path.mimes' => 'The cv file must be a file of type: pdf, doc, docx.',
            'cv_path.max' => 'The cv file may not be greater than :max kilobytes.',
        ];
    }
}
