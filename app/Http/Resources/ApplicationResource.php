<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $serverPath = 'https://coderman.pixela2.com.co/public/storage/';
        return [
            'id' => $this->id,
            'candidate_id' => $this->candidate_id,
            'job_id' => $this->job_id,
            "job_title" => $this->job->title,
            "job_salary" => $this->job->salary,
            'company' => [
                'id' => $this->job->company->id,
                'name' => $this->job->company->user->name,
                'logo' => $serverPath . $this->job->company->logo,
                'banner' =>  $serverPath . $this->job->company->banner,
            ],
            'company_id' => $this->job->company_id,
            'company_name' => $this->job->company->user->name,
            'cover_letter' => $this->cover_letter,
            'application_date' => $this->application_date,
            'rejection_date' => $this->rejection_date,
            'status' => $this->status,

        ];
    }
}
