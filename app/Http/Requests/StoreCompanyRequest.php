<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('company');
    }

    public function rules(): array
    {
        return [
            'contact_person' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'industry' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'nullable|in:Active,Blocked',
            // Archivos base64
            'logo_file_base64' => 'nullable|string',
            'banner_file_base64' => 'nullable|string',
            // Relacionado con las redes sociales (opcional)
            'social_networks' => 'nullable|array',
            'social_networks.*.url' => 'required|url',
            // Validación para el país, estado, ciudad y código postal
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zip_code_id' => 'nullable|exists:zip_codes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_person.required' => 'The contact person field is required.',
            'phone_number.max' => 'The phone number may not be greater than :max characters.',
            'website.url' => 'The website format is invalid.',
            'logo_file_base64.string' => 'The logo file must be a string.',
            'banner_file_base64.string' => 'The banner file must be a string.',
            'social_networks.*.name.required' => 'The social network name field is required.',
            'social_networks.*.url.required' => 'The social network URL field is required.',
            'social_networks.*.url.url' => 'The social network URL format is invalid.',
            'country_id.exists' => 'The selected country is invalid.',
            'state_id.exists' => 'The selected state is invalid.',
            'city_id.exists' => 'The selected city is invalid.',
            'zip_code_id.exists' => 'The selected ZIP code is invalid.',
        ];
    }
}
