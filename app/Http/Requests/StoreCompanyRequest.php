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
        $rules = [
            'contact_person' => 'required|string',
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
            'contact_person.required' => 'The contact person field is required.',
            'phone_number.max' => 'The phone number cannot be more than :max characters.',
            'website.url' => 'The website format is invalid.',
            'logo.string' => 'The logo field must be a string.',
            'banner.string' => 'The banner field must be a string.',
            'video.string' => 'The video field must be a string.',
            'location.country.required' => 'The country is required.',
            'location.state.required' => 'The state is required.',
            'location.city.required' => 'The city is required.',
            'location.zip_code.required' => 'The zip code is required.',
            'location.zip_code.max' => 'The zip code cannot be more than :max characters.',
            'location.dial_code.required' => 'The dial code is required.',
            'location.dial_code.max' => 'The dial code cannot be more than :max characters.',
            'location.iso_alpha_2.required' => 'The ISO alpha 2 code is required.',
            'location.iso_alpha_2.string' => 'The ISO alpha 2 code must be a string.',
            'location.iso_alpha_2.size' => 'The ISO alpha 2 code must be :size characters.',
        ];
    }

}
