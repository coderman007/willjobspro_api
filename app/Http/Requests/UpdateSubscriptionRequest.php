<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'candidate_id' => 'exists:candidates,id',
            'subscription_plan_id' => 'exists:subscription_plans,id',
            'payment_method' => 'in:credit_card,paypal',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:Active,Inactive',
            'payment_status' => 'in:Pending,Completed,Failed',
        ];
    }
}
