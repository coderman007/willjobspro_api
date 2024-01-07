<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'exists:companies,id',
            'job_category_id' => 'exists:job_categories,id',
            'job_type_id' => 'exists:job_types,id',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'title' => 'string|max:255',
            'description' => 'string',
            'posted_date' => 'date',
            'deadline' => 'date',
            'location' => 'string|max:255',
            'salary' => 'numeric',
            'contact_email' => 'email',
            'contact_phone' => 'string|max:20',
            'status' => 'in:Abierto,Cerrado,En Revisión',
        ];
    }
}
