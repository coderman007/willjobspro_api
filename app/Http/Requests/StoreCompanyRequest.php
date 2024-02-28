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
            'user_id' => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zip_code_id' => 'required|exists:zip_codes,id',
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'logo_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Maximum file size: 2 MB
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Maximum file size: 2 MB
            'social_networks' => 'nullable|json',
            'status' => 'required|in:Active,Blocked',
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
            'name.required' => 'The company name field is required.',
            'industry.nullable' => 'The industry field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'website.nullable' => 'The website field is optional.',
            'description.nullable' => 'The description field is optional.',
            'contact_person.required' => 'The contact person field is required.',
            'logo_file.nullable' => 'The logo file field is optional.',
            'banner_file.nullable' => 'The banner file field is optional.',
            'social_networks.nullable' => 'The company social networks field is optional.',
            'status.required' => 'The status field is required.',
        ];
    }
}
