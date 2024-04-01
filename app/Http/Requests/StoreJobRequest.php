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
        $rules = [
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
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
        ];

        if ($this->filled('skills')) {
            $rules['skills'] = ['array'];
            $rules['skills.*'] = ['required', 'exists:skills,id'];
        }

        if ($this->filled('job_types')) {
            $rules['job_types'] = ['array'];
            $rules['job_types.*'] = ['required', 'exists:job_types,id'];
        }

        if ($this->filled('education_levels')) {
            $rules['education_levels'] = ['array'];
            $rules['education_levels.*'] = ['required', 'exists:education_levels,id'];
        }

        if ($this->filled('languages')) {
            $rules['languages'] = ['array'];
            $rules['languages.*.id'] = ['required', 'exists:languages,id'];
            $rules['languages.*.level'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * Customize the validation attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'job_category_id' => 'Job category',
            'title' => 'Title',
            'description' => 'Description',
            'posted_date' => 'Posted date',
            'deadline' => 'Deadline',
            'location' => 'Location',
            'salary' => 'Salary',
            'contact_email' => 'Contact email',
            'contact_phone' => 'Contact phone',
            'experience_required' => 'Experience required',
            'status' => 'Status',
            'subscription_plan_id' => 'Subscription plan',
            'skills.*' => 'Skill',
            'job_types.*' => 'Job type',
            'education_levels.*' => 'Education level',
            'languages.*.id' => 'Language',
            'languages.*.level' => 'Language level',
        ];
    }

    public function messages(): array
    {
        return [
            'job_category_id.required' => 'The job category field is required.',
            'job_category_id.exists' => 'The selected job category is invalid.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'posted_date.required' => 'The posted date field is required.',
            'posted_date.date' => 'The posted date must be a valid date.',
            'deadline.required' => 'The deadline field is required.',
            'deadline.date' => 'The deadline must be a valid date.',
            'location.required' => 'The location field is required.',
            'location.string' => 'The location field must be a string.',
            'salary.required' => 'The salary field is required.',
            'salary.numeric' => 'The salary field must be a numeric value.',
            'salary.min' => 'The salary cannot be a negative value.',
            'contact_email.required' => 'The contact email field is required.',
            'contact_email.email' => 'The contact email must be a valid email address.',
            'contact_phone.required' => 'The contact phone field is required.',
            'experience_required.string' => 'The experience required field must be a string.',
            'status.string' => 'The status field must be a string.',
            'status.in' => 'The selected status is invalid.',
            'subscription_plan_id.exists' => 'The selected subscription plan is invalid.',
            'skills.*.required' => 'At least one skill is required for the job.',
            'skills.*.exists' => 'One or more selected skills are invalid.',
            'job_types.*.required' => 'At least one job type is required.',
            'job_types.*.exists' => 'One or more selected job types are invalid.',
            'education_levels.*.required' => 'At least one education level is required.',
            'education_levels.*.exists' => 'One or more selected education levels are invalid.',
            'languages.*.id.required' => 'At least one language is required.',
            'languages.*.id.exists' => 'One or more selected languages are invalid.',
            'languages.*.level.required' => 'The language level is required for each selected language.',
        ];
    }


}
