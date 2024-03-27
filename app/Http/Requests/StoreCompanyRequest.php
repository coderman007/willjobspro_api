<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('company');
    }

    public function rules(): array
    {
        return [
            'contact_person' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'industry' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'social_networks' => 'nullable|json',
            'status' => 'nullable|in:Active,Blocked',
            'logo_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zip_code_id' => 'nullable|exists:zip_codes,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contact_person.required' => 'The contact person field is required.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'industry.nullable' => 'The industry field is optional.',
            'description.nullable' => 'The description field is optional.',
            'website.nullable' => 'The website field is optional.',
            'social_networks.nullable' => 'The company social networks field is optional.',
            'status.nullable' => 'The status field is optional.',
            'logo_file.nullable' => 'The logo file field is optional.',
            'banner_file.nullable' => 'The banner file field is optional.',
            'country_id.nullable' => 'The country id  is optional',
            'state_id.nullable' => 'The state id  is optional',
            'city_id.nullable' => 'The city id  is optional',
            'zip_code_id.nullable' => 'The zip code id  is optional',
        ];
    }
}
