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

            'candidate' => [
                'id' => $this->candidate_id,
                'gender' => $this->candidate->gender,
                'date_of_birth' => $this->candidate->date_of_birth,
                'location' => [
                    'country' => $this->candidate->user->country->name,
                ],
                'cv_url' => $this->candidate->cv ? $serverPath . $this->candidate->cv : null,
                'profile_photo' => $this->candidate->photo ? $serverPath . $this->candidate->photo : null,
                'cover_letter' => $this->cover_letter,

            ],

            'job' => [
                'id' => $this->job_id,
                'title' => $this->job->title,
                'salary' => $this->job->salary,
            ],

            'application' => [
                'application_id' => $this->id,
                'application_date' => $this->application_date,
                'rejection_date' => $this->rejection_date,
                'status' => $this->status,
            ],

            'company' => [
                'id' => $this->job->company->id,
                'name' => $this->job->company->user->name,
                'logo' => $serverPath . $this->job->company->logo,
                'banner' => $serverPath . $this->job->company->banner,
            ],
        ];
    }
}
