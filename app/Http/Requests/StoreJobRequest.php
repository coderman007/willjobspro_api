<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_category_id' => 'required|exists:job_categories,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'posted_date' => 'required|date',
            'deadline' => 'required|date',
            'location' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'experience_required' => 'nullable|string',
            'status' => 'string|in:Open,Closed,Under Review',
            'education_levels' => 'nullable|string',
            'languages' => 'nullable|string',
            'job_types' => 'nullable|string',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
        ];
    }

    /**
     * Customize the validation attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'company_id' => 'company',
            'job_category_id' => 'job category',
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
