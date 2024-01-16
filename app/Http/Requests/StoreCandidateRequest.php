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
            'status' => 'required|in:Active,Inactive',
        ];
    }
}
