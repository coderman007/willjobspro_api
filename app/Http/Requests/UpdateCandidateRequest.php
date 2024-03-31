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
            'cv_file' => 'nullable|string',
            'photo_file' => 'nullable|string',
            'banner_file' => 'nullable|string',
        ];

        // Reglas de validación adicionales para la actualización
        $rules = $this->addUpdateValidationRules($rules);

        return $rules;
    }

    private function addUpdateValidationRules(array $rules): array
    {
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
            $rules['languages.*.id'] = 'required|numeric';
            $rules['languages.*.level'] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'gender.nullable' => 'El campo género es opcional.',
            'date_of_birth.nullable' => 'El campo fecha de nacimiento es opcional.',
            'phone_number.nullable' => 'El campo número de teléfono es opcional.',
            'expected_salary.nullable' => 'El campo salario esperado es opcional.',
            'status.nullable' => 'El campo estado es opcional.',
            'country_id.exists' => 'El país seleccionado no es válido.',
            'state_id.exists' => 'El estado seleccionado no es válido.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
            'zip_code_id.exists' => 'El código postal seleccionado no es válido.',
            'education_history.*.institution.required' => 'El campo institución es obligatorio para todo el historial académico.',
            'education_history.*.degree_title.required' => 'El campo título de grado es obligatorio para todo el historial académico.',
            'education_history.*.start_date.required' => 'El campo fecha de inicio es obligatorio para todo el historial académico.',
            'education_history.*.end_date.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio para todo el historial académico.',
            'work_experiences.*.company.required' => 'El campo empresa es obligatorio para toda la experiencia laboral.',
            'work_experiences.*.position.required' => 'El campo posición es obligatorio para toda la experiencia laboral.',
            'work_experiences.*.start_date.required' => 'El campo fecha de inicio es obligatorio para toda la experiencia laboral.',
            'work_experiences.*.end_date.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio para toda la experiencia laboral.',
            'social_networks.*.url.required' => 'El campo URL es obligatorio para las redes sociales.',
            'social_networks.*.url.url' => 'El formato de URL no es válido para las redes sociales.',
            'skills.*.exists' => 'La habilidad seleccionada no es válida.',
            'languages.*.id.required' => 'El campo ID del idioma es obligatorio para todos los idiomas.',
            'languages.*.level.required' => 'El campo nivel de idioma es obligatorio para todos los idiomas.',
        ];
    }
}
