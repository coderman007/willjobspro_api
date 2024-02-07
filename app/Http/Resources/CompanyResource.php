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
            'name' => $this->name,
            'email' => $this->user->email,
            'jobs' => $this->user->jobs,
            'industry' => $this->industry,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'website' => $this->website,
            'description' => $this->description,
            'contact_person' => $this->contact_person,
            'logo_path' => $this->logo_path,
            'social_networks' => $this->social_networks,
            'status' => $this->status,
        ];
    }
}
