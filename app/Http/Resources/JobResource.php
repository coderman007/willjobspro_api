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
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->user->name,
                'address' => $this->company->user->address ?? null,
                'logo' => $this->company->logo ? $serverPath . $this->company->logo : null,
                'banner' =>  $this->company->banner ? $serverPath . $this->company->banner : null,
            ],
            'job_category' => $this->jobCategory->name,
            'job_types' => $this->getAttribute('jobTypes') ?? null,
            'languages' => $this->getAttribute('languages') ?? null,
            'education_levels' => $this->getAttribute('educationLevels') ?? null,
            'skills' => $this->getAttribute('skills') ?? null,
            'total_applications' => $this->applications->count(),
        ];

        // Verificar si se están aplicando filtros por ubicación y la ubicación de la oferta es nula
        if ($request->filled('country_name') && $request->filled('state_name') && $request->filled('city_name') &&
            (!$this->country || !$this->state || !$this->city || !$this->zipCode)
        ) {
            $data['location'] = null; // Ubicación no proporcionada
            $data['message'] = 'La ubicación corresponde a la ubicación de la compañía';
        } else {
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
        }

        return $data;
    }
}
