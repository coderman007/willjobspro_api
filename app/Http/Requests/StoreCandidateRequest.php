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
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'work_experience' => 'required|string',
            'education' => 'required|string',
            'skills' => 'required|string',
            'certifications' => 'required|string',
            'languages' => 'required|string',
            'references' => 'required|string',
            'expected_salary' => 'required|numeric|min:0',
            'cv_path' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'status' => 'required|in:Activo,Inactivo,Pendiente',
        ];
    }
}
