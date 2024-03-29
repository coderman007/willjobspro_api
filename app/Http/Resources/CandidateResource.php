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
            'candidate_id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            'expected_salary' => $this->expected_salary,
            'status' => $this->status,
            'country' => $this->user->country,
            'state' => $this->user->state,
            'city' => $this->user->city,
            'zip_code' => $this->user->zipCode,
            'skills' => $this->skills,
            'education_levels' => $this->educationLevels,
            'languages' => $this->languages,
            'social_networks' => $this->socialNetworks,
            'cv' => $this->cv_file ? url('storage/' . $this->cv_file) : null,
            'profile_photo' => $this->photo_file ? url('storage/' . $this->photo_file) : null,
            'banner' => $this->banner_file ? url('storage/' . $this->banner_file) : null,
        ];
    }
}
