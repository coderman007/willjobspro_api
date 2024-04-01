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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'contact_person' => $this->contact_person,
            'phone_number' => $this->phone_number,
            'industry' => $this->industry,
            'description' => $this->description,
            'website' => $this->website,
            'status' => $this->status,
            'country' => $this->user->country,
            'state' => $this->user->state,
            'city' => $this->user->city,
            'zip_code' => $this->user->zipCode,
            'social_networks' => $this->user->socialNetworks,
            'logo' => $this->logo_file ? url('storage/' . $this->logo_file) : null,
            'banner' => $this->banner_file ? url('storage/' . $this->banner_file) : null,
        ];
    }
}
