<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'country' => $this->user->country,
            'state' => $this->user->state,
            'city' => $this->user->city,
            'zip_code' => $this->user->zipCode,
            'address' => $this->user->address,
            'skills' => $this->skills,
            'education_histories' => $this->educationHistories->map(function ($education) {
                return [
                    'id' => $education->id,
                    'education_level_id' => $education->education_level_id,
                    'education_level_name' => $education->educationLevel->name,
                    'institution' => $education->institution,
                    'field_of_study' => $education->field_of_study,
                    'start_date' => $education->start_date,
                    'end_date' => $education->end_date,
                ];
            }),
            'work_experiences' => $this->workExperiences,
            'languages' => $this->languages,
            'social_networks' => $this->user->socialNetworks,
            'cv' => $this->cv ? url('storage/' . $this->cv) : null,
            'profile_photo' => $this->photo ? url('storage/' . $this->photo) : null,
            'banner' => $this->banner ? url('storage/' . $this->banner) : null,
        ];
    }
}
