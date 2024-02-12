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
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'email' => $this->user->email,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'work_experience' => $this->work_experience,
            'education' => $this->education,
            'certifications' => $this->certifications,
            'languages' => $this->languages,
            'references' => $this->references,
            'expected_salary' => $this->expected_salary,
            'cv_path' => $this->cv_path,
            'photo_path' => $this->photo_path,
            'banner_path' => $this->banner_path,
            'social_networks' => $this->social_networks,
            'status' => $this->status,
        ];
    }
}
