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
            'candidate_id' => 'required|exists:candidates,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'nullable|in:credit_card,paypal',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:Active,Blocked',
            'payment_status' => 'required|in:Pending,Completed,Failed',
        ];
    }
}
