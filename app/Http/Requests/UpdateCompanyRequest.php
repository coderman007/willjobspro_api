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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'exists:users,id',
            'country_id' => 'exists:countries,id',
            'state_id' => 'exists:states,id',
            'city_id' => 'exists:cities,id',
            'zip_code_id' => 'exists:zip_codes,id',
            'name' => 'string|max:255',
            'industry' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'string|max:255',
            'logo_file' => 'nullable|string|max:255',
            'banner_file' => 'nullable|string|max:255',
            'social_networks' => 'nullable|json',
            'status' => 'in:Active,Blocked',
        ];
    }
}
