<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificar si el usuario tiene el rol 'candidate'
        return true;
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
            'job_id' => 'required|exists:jobs,id',
            'cover_letter' => 'nullable|string|max:500',
            'status' => 'required|in:Pending,Reviewed,Accepted,Rejected',
            'application_date' => 'nullable|date',
            'rejection_date' => 'nullable|date|after_or_equal:application_date',
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'candidate_id.required' => 'The candidate is required.',
            'candidate_id.exists' => 'The candidate does not exist.',
            'job_id.required' => 'The job is required.',
            'job_id.exists' => 'The job does not exist.',
            'cover_letter.required' => 'The cover letter is required.',
            'cover_letter.string' => 'The cover letter must be a string.',
            'cover_letter.max' => 'The cover letter must be less than 500 characters.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status is invalid.',
            'application_date.date' => 'The application date is invalid.',
            'rejection_date.date' => 'The rejection date is invalid.',
            'rejection_date.after_or_equal' => 'The rejection date must
            be after the application date.',
        ];
    }
}
