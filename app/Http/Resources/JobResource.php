<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getAttribute(string $string)
 */
class JobResource extends JsonResource
{
    public function toArray($request): array
    {
        $serverPath = 'https://coderman.pixela2.com.co/public/storage/';

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description ?? null,
            'posted_date' => $this->posted_date,
            'deadline' => $this->deadline,
            'salary' => $this->salary,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'experience_required' => $this->experience_required,
            'status' => $this->status,
            'image' => $this->image ? url('storage/' . $this->image) : null,
            'video' => $this->video ? url('storage/' . $this->video) : null,
            // 'company' => new CompanyResource($this->whenLoaded('company')),
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->user->name,
                'industry' => $this->company->company_name,
                'address' => $this->company->user->address ?? null,
                'logo' => $this->company->logo ? $serverPath . $this->company->logo : null,
                'banner' => $this->company->banner ? $serverPath . $this->company->banner : null,
            ],
            'benefits' => $this->getAttribute('benefits') ?? null,
            'job_category' => $this->jobCategory->name,
            'job_types' => $this->getAttribute('jobTypes') ?? null,
            'languages' => $this->getAttribute('languages') ?? null,
            'education_levels' => $this->getAttribute('educationLevels') ?? null,
            'skills' => $this->getAttribute('skills') ?? null,
            'total_applications' => $this->applications->count(),
        ];

        // Verificar si la ubicación está presente
        if ($this->country && $this->state && $this->city && $this->zipCode) {
            $data['location'] = [
                'country' => [
                    'name' => $this->country->name,
                    'dial_code' => $this->country->dial_code,
                    'iso_alpha_2' => $this->country->iso_alpha_2
                ],
                'state' => $this->state->name,
                'city' => $this->city->name,
                'zip_code' => $this->zipCode->code,
            ];
        } else {
            $data['location'] = null; // Ubicación no proporcionada
        }

        return $data;
    }
}
