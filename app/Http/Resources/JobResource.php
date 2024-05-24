<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getAttribute(string $string)
 * @property mixed $company
 * @property mixed $id
 * @property mixed $title
 * @property mixed $posted_date
 * @property mixed $deadline
 * @property mixed $salary
 * @property mixed $contact_email
 * @property mixed $contact_phone
 * @property mixed $experience_required
 * @property mixed $status
 * @property mixed $image
 * @property mixed $video
 * @property mixed $jobCategory
 * @property mixed $applications
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

        // Obtener la información de ubicación
        $location = [
            'country' => $this->getAttribute('country')->name ?? null,
            'state' => $this->getAttribute('state')->name ?? null,
            'city' => $this->getAttribute('city')->name ?? null,
            'zip_code' => $this->getAttribute('zipCode')->code ?? null,
        ];

        // Combinar la información de ubicación con los datos existentes
        return array_merge($data, ['location' => $location]);
    }

}
