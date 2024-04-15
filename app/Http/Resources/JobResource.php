<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'posted_date' => $this->posted_date,
            'deadline' => $this->deadline,
            'location' => [
                'country' => [
                    'name' => $this->country->name,
                    'dial_code' => $this->country->dial_code,
                    'iso_alpha_2' => $this->country->iso_alpha_2],
                'state' => $this->state->name,
                'city' => $this->city->name,
                'zip_code' => $this->zipCode->code,
            ],
            'salary' => $this->salary,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'experience_required' => $this->experience_required,
            'status' => $this->status,
            'job_types' => $this->getAttribute('jobTypes'),
            'languages' => $this->getAttribute('languages'),
            'education_levels' => $this->getAttribute('educationLevels'),
            'skills' => $this->getAttribute('skills'),

        ];
    }
}

