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
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'logo_path' => 'nullable|string|max:255',
            'banner_path' => 'nullable|string|max:255',
            'company_social_networks' => 'nullable|json',
            'status' => 'required|in:Active,Inactive',
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
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The user field does not exist.',
            'company_name.required' => 'The company name field is required.',
            'industry.nullable' => 'The industry field is optional.',
            'address.nullable' => 'The address field is optional.',
            'phone_number.nullable' => 'The phone number field is optional.',
            'website.nullable' => 'The website field is optional.',
            'description.nullable' => 'The description field is optional.',
            'contact_person.required' => 'The contact person field is required.',
            'logo_path.nullable' => 'The logo path field is optional.',
            'banner_path.nullable' => 'The banner path field is optional.',
            'company_social_networks.nullable' => 'The company social networks field is optional.',
            'status.required' => 'The status field is required.',
        ];
    }
}
