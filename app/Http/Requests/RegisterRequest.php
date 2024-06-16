<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:5'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                // ->uncompromised(),
                'confirmed',
            ],
            'role' => 'required',
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
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.required' => 'El campo de contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.mixed_case' => 'La contraseña debe contener caracteres en mayúscula y minúscula.',
            'password.numbers' => 'La contraseña debe contener números.',
            'password.symbols' => 'La contraseña debe contener símbolos.',
            'role.required' =>
            "Debe indicar si eres una empresa o un candidato.",
            'role.in' => "Por favor, elige entre 'Empresa' o
            'Candidato'.",
        ];
    }
}
