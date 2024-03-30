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
        $rules = [
            'gender' => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'expected_salary' => 'nullable|numeric',
            'status' => 'nullable|in:Active,Blocked',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zip_code_id' => 'nullable|exists:zip_codes,id',
            // Archivos base64
            'cv_file' => 'nullable|string',
            'photo_file' => 'nullable|string',
            'banner_file' => 'nullable|string',
        ];
        // Reglas de validación para el historial académico (opcional)
        if ($this->filled('education_history')) {
            $rules['education_history'] = 'nullable|array';
            $rules['education_history.*.institution'] = 'required|string';
            $rules['education_history.*.degree_title'] = 'required|string';
            $rules['education_history.*.field_of_study'] = 'nullable|string';
            $rules['education_history.*.start_date'] = 'required|date';
            $rules['education_history.*.end_date'] = 'nullable|date|after_or_equal:education_history.*.start_date';
        }
        // Reglas de validación para la experiencia laboral (opcional)
        if ($this->filled('work_experiences')) {
            $rules['work_experiences'] = 'nullable|array';
            $rules['work_experiences.*.company'] = 'required|string';
            $rules['work_experiences.*.position'] = 'required|string';
            $rules['work_experiences.*.responsibility'] = 'nullable|string';
            $rules['work_experiences.*.start_date'] = 'required|date';
            $rules['work_experiences.*.end_date'] = 'nullable|date|after_or_equal:work_experiences.*.start_date';
        }
        // Reglas de validación para las redes sociales (opcional)
        if ($this->filled('social_networks')) {
            $rules['social_networks'] = 'nullable|array';
            $rules['social_networks.*.url'] = 'required|url';
        }
        // Reglas de validación para las habilidades de candidatos (opcional)
        if ($this->filled('skills')) {
            $rules['skills'] = 'nullable|array';
            $rules['skills.*'] = 'numeric|exists:skills,id';
        }
        // Reglas de validación para los idiomas (opcional)
        if ($this->filled('languages')) {
            $rules['languages'] = 'nullable|array';
            $rules['languages.*'] = 'numeric|exists:languages,id';
            $rules['languages.*.level'] = 'required|string|in:basic,intermediate,advanced,native';
        }

        return $rules;
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
            'education_history.*.institution.required' => 'The institution field is required.',
            'education_history.*.degree_title.required' => 'The degree title field is required.',
            'education_history.*.start_date.required' => 'The start date field is required.',
            'education_history.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'work_experiences.*.company.required' => 'The company field is required.',
            'work_experiences.*.position.required' => 'The position field is required.',
            'work_experiences.*.start_date.required' => 'The start date field is required.',
            'work_experiences.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'social_networks.*.url.required' => 'The URL field is required for social networks.',
            'social_networks.*.url.url' => 'The URL format is invalid for social networks.',
            'cv_file.nullable' => 'The cv file field is optional.',
            'photo_file.nullable' => 'The photo file field is optional.',
            'banner_file.nullable' => 'The banner file field is optional.',
        ];
    }
}
