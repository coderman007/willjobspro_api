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
            'company_name' => 'nullable|string',
            'contact_person' => 'string',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'industry' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'nullable|in:Active,Blocked',
            'logo' => 'nullable|string', // Archivo base64
            'banner' => 'nullable|string', // Archivo base64
            'video' => 'nullable|string', // Archivo base64
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
            'company_name.nullable' => 'The company name field is optional.',
            'company_name.string' => 'The company name field must be a string.',
            'contact_person.string' => 'The contact person field must be a string.',
            'phone_number.max' => 'The phone number may not be greater than :max characters.',
            'website.url' => 'The website format is invalid.',
            'logo.string' => 'The logo field must be a string.',
            'banner.string' => 'The banner field must be a string.',
            'video.string' => 'The banner field must be a string.',
            'social_networks.*.url.required' => 'The social network URL field is required.',
            'social_networks.*.url.url' => 'The social network URL format is invalid.',
            'location.country.required' => 'The country field is required.',
            'location.state.required' => 'The state field is required.',
            'location.city.required' => 'The city field is required.',
            'location.zip_code.required' => 'The zip code field is required.',
            'location.zip_code.max' => 'The zip code may not be greater than :max characters.',
            'location.dial_code.required' => 'The dial code field is required.',
            'location.dial_code.max' => 'The dial code may not be greater than :max characters.',
            'location.iso_alpha_2.required' => 'The ISO alpha 2 code is required.',
            'location.iso_alpha_2.string' => 'The ISO alpha 2 code must be a string.',
            'location.iso_alpha_2.size' => 'The ISO alpha 2 code must be :size characters.',
        ];
    }
}
