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
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'posted_date' => 'sometimes|required|date',
            'deadline' => 'sometimes|required|date',
            'salary' => 'sometimes|required|numeric|min:0',
            'contact_email' => 'sometimes|required|email',
            'contact_phone' => 'sometimes|required|string',
            'experience_required' => 'sometimes|nullable|string',
            'subscription_plan_id' => 'sometimes|nullable|exists:subscription_plans,id',
            'skills' => 'sometimes|array',
            'skills.*' => 'sometimes|required|exists:skills,id',
            'job_types' => 'sometimes|array',
            'job_types.*' => 'sometimes|required|exists:job_types,id',
            'education_levels' => 'sometimes|array',
            'education_levels.*' => 'sometimes|required|exists:education_levels,id',
            'languages' => 'sometimes|array',
            'languages.*.id' => 'sometimes|required|exists:languages,id',
            'languages.*.level' => 'sometimes|required|string',
            'location.country' => 'sometimes|string',
            'location.state' => 'sometimes|string',
            'location.city' => 'sometimes|string',
            'location.zip_code' => 'sometimes|string|max:10',
            'location.dial_code' => 'sometimes|string|max:10',
            'location.iso_alpha_2' => 'sometimes|string|max:2',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'posted_date' => 'Posted date',
            'deadline' => 'Deadline',
            'salary' => 'Salary',
            'contact_email' => 'Contact email',
            'contact_phone' => 'Contact phone',
            'experience_required' => 'Experience required',
            'subscription_plan_id' => 'Subscription plan',
            'skills.*' => 'Skill',
            'job_types.*' => 'Job type',
            'education_levels.*' => 'Education level',
            'languages.*.id' => 'Language',
            'languages.*.level' => 'Language level',
            'location.country' => 'Country',
            'location.state' => 'State',
            'location.city' => 'City',
            'location.zip_code' => 'Zip code',
            'location.dial_code' => 'Dial code',
            'location.iso_alpha_2' => 'ISO alpha 2',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'posted_date.required' => 'The posted date field is required.',
            'posted_date.date' => 'The posted date must be a valid date.',
            'deadline.required' => 'The deadline field is required.',
            'deadline.date' => 'The deadline must be a valid date.',
            'salary.required' => 'The salary field is required.',
            'salary.numeric' => 'The salary field must be a numeric value.',
            'salary.min' => 'The salary cannot be a negative value.',
            'contact_email.required' => 'The contact email field is required.',
            'contact_email.email' => 'The contact email must be a valid email address.',
            'contact_phone.required' => 'The contact phone field is required.',
            'experience_required.string' => 'The experience required field must be a string.',
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
            'location.country.string' => 'The country must be a string.',
            'location.state.string' => 'The state must be a string.',
            'location.city.string' => 'The city must be a string.',
            'location.zip_code.string' => 'The zip code must be a string.',
            'location.dial_code.string' => 'The dial code must be a string.',
            'location.iso_alpha_2.string' => 'The ISO alpha 2 must be a string.',
        ];
    }
}
