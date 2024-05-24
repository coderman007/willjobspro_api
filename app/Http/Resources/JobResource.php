<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->user->name,
                'company_name' => $this->company->company_name,
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

        // Verificar si la ubicación está presente en la oferta de trabajo
        if ($this->location) {
            // La oferta de trabajo tiene una ubicación definida
            $locationData = [
                'country' => [
                    'name' => $this->location->country->name,
                    'dial_code' => $this->location->country->dial_code,
                    'iso_alpha_2' => $this->location->country->iso_alpha_2
                ],
                'state' => $this->location->state->name,
                'city' => $this->location->city->name,
                'zip_code' => $this->location->zipCode->code,
            ];
        } else {
            // La oferta de trabajo no tiene una ubicación definida, obtenemos la ubicación de la compañía
            $companyLocation = $this->getCompanyLocation();
            if ($companyLocation) {
                // La compañía tiene una ubicación definida
                $locationData = $companyLocation;
            } else {
                // Ni la oferta de trabajo ni la compañía tienen una ubicación definida
                $locationData = null;
            }
        }

        $data['location'] = $locationData;

        return $data;
    }

    protected function getCompanyLocation(): ?array
    {
        // Obtener la ubicación de la compañía asociada a la oferta de trabajo
        $company = $this->company;

        if ($company && $company->location) {
            // La compañía tiene una ubicación definida
            return [
                'country' => [
                    'name' => $company->location->country->name,
                    'dial_code' => $company->location->country->dial_code,
                    'iso_alpha_2' => $company->location->country->iso_alpha_2
                ],
                'state' => $company->location->state->name,
                'city' => $company->location->city->name,
                'zip_code' => $company->location->zipCode->code,
            ];
        }

        return null; // La compañía no tiene una ubicación definida
    }
}
