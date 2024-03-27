<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
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
            'contact_person' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'industry' => 'string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'logo_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Ejemplo: JPEG, PNG con un tamaño máximo de 2 MB
            'social_networks' => 'nullable|json',
            'status' => 'in:Active,Blocked',
            'country_id' => 'exists:countries,id',
            'state_id' => 'exists:states,id',
            'city_id' => 'exists:cities,id',
            'zip_code_id' => 'exists:zip_codes,id',
        ];
    }
}
