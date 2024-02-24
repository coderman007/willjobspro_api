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
            'job_type_ids' => 'array|exists:job_types,id',
            'education_level_id' => 'exists:education_levels,id',
            'subscription_plan_id' => 'exists:subscription_plans,id',
            'title' => 'string|max:255',
            'description' => 'string',
            'posted_date' => 'date',
            'deadline' => 'date|after:posted_date',
            'location' => 'string|max:255',
            'salary' => 'numeric',
            'contact_email' => 'email',
            'contact_phone' => 'string|max:20',
            'experience_required' => 'nullable|integer',
            'status' => 'in:Open,Closed,Under Review',
        ];
    }
}
