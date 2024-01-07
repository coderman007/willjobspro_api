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
            'company_name' => 'string|max:255',
            'industry' => 'string|max:255',
            'address' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'website' => 'nullable|string|max:255',
            'description' => 'string',
            'contact_person' => 'string|max:255',
            'logo_path' => 'nullable|string|max:255',
            'status' => 'in:Activo,Inactivo,Pendiente',
        ];
    }
}
