<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cambiado a true si todos los usuarios pueden suscribirse
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:credit_card,paypal', // Ejemplo de métodos de pago permitidos
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:active,inactive', // Ejemplo de estados permitidos
            'payment_status' => 'required|in:pending,completed,failed', // Ejemplo de estados de pago permitidos
        ];
    }
}
