<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Verificar si el usuario está autenticado y tiene el rol de candidato
        return auth()->check() && auth()->user()->hasRole('candidate');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Definir las reglas de validación para la actualización del candidato
        $rules = [
            'gender' => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string|max:20',
            'expected_salary' => 'nullable|numeric',
            // Archivos base64
            'cv' => 'nullable|string',
            'photo' => 'nullable|string',
            'banner' => 'nullable|string',
        ];

        if ($this->filled('education_history')) {
            $rules['education_history'] = 'nullable|array';
            $rules['education_history.*.education_level_id'] = 'required|exists:education_levels,id';
            $rules['education_history.*.institution'] = 'nullable|string';
            $rules['education_history.*.field_of_study'] = 'nullable|string';
            $rules['education_history.*.start_date'] = 'nullable|date';
            $rules['education_history.*.end_date'] = 'nullable|date|after_or_equal:education_history.*.start_date';
        }

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
            $rules['languages.*.id'] ='required|numeric';
            $rules['languages.*.level'] = 'required|string';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        // Definir mensajes de error personalizados para las reglas de validación
        return [
            'gender.nullable' => 'The gender field is optional.',
            'date_of_birth.nullable' => 'The date of birth field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'address.nullable' => 'The address field is optional.',
            'expected_salary.nullable' => 'The expected salary field is optional.',
            'location.nullable' => 'The location field is optional.',
            'location.country.required' => 'El país es obligatorio.',
            'location.country.string' => 'El país debe ser una cadena de texto.',
            'location.state.required' => 'El estado es obligatorio.',
            'location.state.string' => 'El estado debe ser una cadena de texto.',
            'location.city.required' => 'La ciudad es obligatoria.',
            'location.city.string' => 'La ciudad debe ser una cadena de texto.',
            'location.zip_code.required' => 'El código postal es obligatorio.',
            'location.zip_code.string' => 'El código postal debe ser una cadena de texto.',
            'education_history.*.institution.nullable' => 'The institution field is optional.',
            'education_history.*.field_of_study.nullable' => 'The field of study field is optional.',
            'education_history.*.start_date.nullable' => 'The start date field is optional.',
            'education_history.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
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
