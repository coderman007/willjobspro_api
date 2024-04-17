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
                'user_id' => $this->candidate->user_id,
                'gender' => $this->candidate->gender ?? null,
                'date_of_birth' => $this->candidate->date_of_birth ?? null,
                'country' => $this->candidate->user->country->name ?? null,
                'expected_salary' => $this->candidate->expected_salary ?? null,
                'cv' => $this->candidate->cv ? $serverPath . $this->candidate->cv : null,
                'profile_photo' => $this->candidate->photo ? $serverPath . $this->candidate->photo : null,
                'cover_letter' => $this->cover_letter ?? null,
            ],

            'job' => [
                'id' => $this->job_id,
                'title' => $this->job->title ?? null,
                'salary' => $this->job->salary ?? null,
            ],

            'application' => [
                'application_id' => $this->id,
                'application_date' => $this->application_date,
                'rejection_date' => $this->rejection_date ?? null,
                'status' => $this->status,
            ],

            'company' => [
                'id' => $this->job->company->id,
                'name' => $this->job->company->user->name ?? null,
                'logo' => $serverPath . ($this->job->company->logo ?? null),
            ],
        ];
    }
}
