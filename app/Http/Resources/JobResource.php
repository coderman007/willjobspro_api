<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'job_offer_id' => $this->id,
            'company_id' => $this->company->id,
            'company_name' => $this->company->user->name,
            'job_category_id' => $this->jobCategory->id,
            'job_category_name' => $this->jobCategory->name,
            'title' => $this->title,
            'description' => $this->description,
            'posted_date' => $this->posted_date,
            'deadline' => $this->deadline,
            'location' => $this->location,
            'salary' => $this->salary,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'experience_required' => $this->experience_required,
            'status' => $this->status,
            'skills' => $this->skills,
            'education_levels' => $this->educationLevels,
            'job_types' => $this->jobTypes,
            'languages' => $this->languages,
        ];
    }
}

