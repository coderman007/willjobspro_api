<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Lógica de autorización
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'posted_date' => 'sometimes|date',
            'deadline' => 'sometimes|date',
            'location' => 'sometimes|string',
            'salary' => 'sometimes|numeric|min:0',
            'contact_email' => 'sometimes|email',
            'contact_phone' => 'sometimes|string',
            'experience_required' => 'nullable|string',
            'status' => 'sometimes|string|in:Open,Closed,Under Review',
            'education_levels' => 'nullable|string',
            'languages' => 'nullable|string',
            'job_types' => 'nullable|string',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'posted_date' => 'posted date',
            'deadline' => 'deadline',
            'contact_email' => 'contact email',
            'contact_phone' => 'contact phone',
            'experience_required' => 'experience required',
            'subscription_plan_id' => 'subscription plan',
            'education_levels' => 'education levels (comma-separated)',
            'languages' => 'languages (ID:level, separated by commas)',
            'job_types' => 'job types (comma-separated)',
        ];
    }
}
