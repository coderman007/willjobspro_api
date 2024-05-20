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
            'address' => 'nullable|string',
            // Archivos base64
            'cv' => 'nullable|string',
            'photo' => 'nullable|string',
            'banner' => 'nullable|string',
        ];

        // Reglas de validación para la ubicación, si es proporcionada
         if ($this->filled('location')) {
             $rules['location'] = 'nullable|array';
             $rules['location.country'] = 'sometimes|required|string';
             $rules['location.state'] = 'sometimes|required|string';
             $rules['location.city'] = 'sometimes|required|string';
             $rules['location.zip_code'] = 'sometimes|required|string';
             $rules['location.iso_alpha_2'] = 'sometimes|required|string|max:2';
             $rules['location.dial_code'] = 'sometimes|required|string';
         }

        // Reglas de validación para el historial académico si se proporciona información sobre él
        if ($this->filled('education_history')) {
            $rules['education_history'] = 'nullable|array';
            $rules['education_history.*.education_level_id'] = 'required|exists:education_levels,id';
            $rules['education_history.*.institution'] = 'nullable|string';
            $rules['education_history.*.field_of_study'] = 'nullable|string';
            $rules['education_history.*.start_date'] = 'nullable|date';
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
            $rules['languages.*.id'] = 'required|numeric';
            $rules['languages.*.level'] = 'required|string';

        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'gender.nullable' => 'The gender field is optional.',
            'date_of_birth.nullable' => 'The date of birth field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'education_level_id.exists' => 'The education level is required if education history is provided.',
            'education_history.*.institution.nullable' => 'The institution field is optional.',
            'education_history.*.field_of_study.nullable' => 'The field of study field is optional.',
            'education_history.*.start_date.nullable' => 'The start date field is optional.',
            'education_history.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'location.nullable' => 'The location field is optional.',
            'location.country.required' => 'El país es obligatorio.',
            'location.country.string' => 'El país debe ser una cadena de texto.',
            'location.state.required' => 'El estado es obligatorio.',
            'location.state.string' => 'El estado debe ser una cadena de texto.',
            'location.city.required' => 'La ciudad es obligatoria.',
            'location.city.string' => 'La ciudad debe ser una cadena de texto.',
            'location.zip_code.required' => 'El código postal es obligatorio.',
            'location.zip_code.string' => 'El código postal debe ser una cadena de texto.',

            'work_experiences.*.company.required' => 'The company field is required.',
            'work_experiences.*.position.required' => 'The position field is required.',
            'work_experiences.*.start_date.required' => 'The start date field is required.',
            'work_experiences.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'social_networks.*.url.required' => 'The URL field is required for social networks.',
            'social_networks.*.url.url' => 'The URL format is invalid for social networks.',
            'cv.nullable' => 'The cv file field is optional.',
            'photo.nullable' => 'The photo file field is optional.',
            'banner.nullable' => 'The banner file field is optional.',
        ];
    }
}
