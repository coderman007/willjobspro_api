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
            'description' => $this->description,
            'posted_date' => $this->posted_date,
            'deadline' => $this->deadline,
            'salary' => $this->salary,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'experience_required' => $this->experience_required,
            'status' => $this->status,
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->user->name,
                'logo' => $serverPath . $this->company->logo,
                'banner' =>  $serverPath . $this->company->banner,
                'banner_proof' => $this->company->banner ? url('storage/' . $this->banner) : null,
            ],
            'job_types' => $this->getAttribute('jobTypes'),
            'languages' => $this->getAttribute('languages'),
            'education_levels' => $this->getAttribute('educationLevels'),
            'skills' => $this->getAttribute('skills'),
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
            $data['location'] = []; // Ubicación no proporcionada
        }

        return $data;
    }
}
