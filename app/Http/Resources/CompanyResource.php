<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'company_id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->user->email,
            // 'jobs' => $this->jobs,
            'industry' => $this->industry,
            'phone_number' => $this->phone_number,
            'website' => $this->website,
            'description' => $this->description,
            'contact_person' => $this->contact_person,
            'logo' => $this->logo_path ? url('storage/' . $this->logo_path) : null,
            'banner' => $this->banner_path ? url('storage/' . $this->banner_path) : null,
            'social_networks' => $this->social_networks,
            'country' => $this->user->country->name,
            'state' => $this->user->state->name,
            'city' => $this->user->city->name,
            'zip_code' => $this->user->zipCode->code,
            'status' => $this->status,
        ];
    }
}
