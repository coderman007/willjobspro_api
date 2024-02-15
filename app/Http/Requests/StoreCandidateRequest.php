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
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'work_experience' => 'nullable|string',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'languages' => 'nullable|string',
            'references' => 'nullable|string',
            'expected_salary' => 'nullable|numeric|min:0',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // PDF, DOC, DOCX, tamaño máximo de 2MB
            'photo_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // JPEG, PNG, tamaño máximo de 2MB
            'banner_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Opcional: JPEG, PNG, tamaño máximo de 2MB
            'social_networks' => 'nullable|json',
            'status' => 'required|in:Active,Blocked',
        ];
    }
}
