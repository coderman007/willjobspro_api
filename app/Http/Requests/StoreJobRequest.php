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
            'company_id' => 'required|exists:companies,id',
            'job_category_id' => 'required|exists:job_categories,id',
            'job_type_ids' => 'required|array|exists:job_types,id',
            'education_level_id' => 'required|exists:education_levels,id',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'posted_date' => 'required|date',
            'deadline' => 'required|date|after:posted_date',
            'location' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'experience_required' => 'nullable|integer',
            'status' => 'required|in:Open,Closed,Under Review',
        ];
    }
}
