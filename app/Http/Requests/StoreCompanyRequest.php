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
            'status' => 'required|in:Active,Inactive',
        ];
    }
}
