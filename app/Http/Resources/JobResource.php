<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->user->name,
                // Otros atributos de la empresa si es necesario
            ],
            'job_category' => [
                'id' => $this->jobCategory->id,
                'name' => $this->jobCategory->name,
                // Otros atributos de la categoría de trabajo si es necesario
            ],
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
            'applied' => $this->getAttribute('applied'),
            'job_types' => $this->getAttribute('job_types'),
            'languages' => $this->getAttribute('languages'),
        ];
    }
}

