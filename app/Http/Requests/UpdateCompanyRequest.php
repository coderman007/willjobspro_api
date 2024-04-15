<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('company');
    }

    public function rules(): array
    {
        $rules = [
            'contact_person' => 'string',
            'phone_number' => 'nullable|string|max:20',
            'industry' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'nullable|in:Active,Blocked',
            'logo' => 'nullable|string', // Archivo base64
            'banner' => 'nullable|string', // Archivo base64
        ];

        // Reglas de validación para las redes sociales (opcional)
        if ($this->filled('social_networks')) {
            $rules['social_networks'] = 'nullable|array';
            $rules['social_networks.*.url'] = 'required|url';
        }

        // Reglas de validación para las ubicaciones (opcional)
        if ($this->filled('location')) {
            $locationRules = [
                'location.country' => 'required|string',
                'location.state' => 'required|string',
                'location.city' => 'required|string',
                'location.zip_code' => 'required|string|max:10',
                'location.dial_code' => 'required|string|max:10',
                'location.iso_alpha_2' => 'required|string|size:2',
            ];

            $rules = array_merge($rules, $locationRules);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'contact_person.string' => 'El campo persona de contacto debe ser una cadena.',
            'phone_number.max' => 'El número de teléfono no puede tener más de :max caracteres.',
            'website.url' => 'El formato del sitio web no es válido.',
            'logo.string' => 'El archivo del logotipo debe ser una cadena.',
            'banner.string' => 'El archivo del banner debe ser una cadena.',
            'social_networks.*.url.required' => 'El campo URL de la red social es obligatorio.',
            'social_networks.*.url.url' => 'El formato de la URL de la red social no es válido.',
            'location.country.required' => 'El país es obligatorio.',
            'location.state.required' => 'El estado es obligatorio.',
            'location.city.required' => 'La ciudad es obligatoria.',
            'location.zip_code.required' => 'El código postal es obligatorio.',
            'location.zip_code.max' => 'El código postal no puede tener más de :max caracteres.',
            'location.dial_code.required' => 'El dial code es obligatorio.',
            'location.dial_code.max' => 'El dial code no puede tener más de :max caracteres.',
            'location.iso_alpha_2.required' => 'El código ISO alpha 2 es obligatorio.',
            'location.iso_alpha_2.string' => 'El código ISO alpha 2 debe ser una cadena.',
            'location.iso_alpha_2.size' => 'El código ISO alpha 2 debe tener :size caracteres.',
        ];
    }
}
