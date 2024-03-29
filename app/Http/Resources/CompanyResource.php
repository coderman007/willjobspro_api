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
            'email' => $this->user->email,
            'contact_person' => $this->contact_person,
            'industry' => $this->industry,
            'phone_number' => $this->phone_number,
            'website' => $this->website,
            'description' => $this->description,
//            'country' => $this->user->country->name,
//            'state' => $this->user->state->name,
//            'city' => $this->user->city->name,
//            'zip_code' => $this->user->zipCode->code,
            'social_networks' => $this->socialNetworks,
            'status' => $this->status,
            'logo' => $this->logo_file ? url('storage/' . $this->logo_file) : null,
            'banner' => $this->banner_file ? url('storage/' . $this->banner_file) : null,
        ];
    }
}
