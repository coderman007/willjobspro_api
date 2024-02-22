<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
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
            'cover_letter' => 'nullable|string|max:500',
            // Aquí tengo que validar que la fecha de rechazo solo sea actualizada por la compañía.
            // También, debo hacer que se actualice el estado en razón de las acciones de la compañía que publicó la oferta de trabajo.
            'status' => 'required|in:Pending,Reviewed,Accepted,Rejected',
            'rejection_date' => 'nullable|date|after_or_equal:application_date',
        ];
    }
}
