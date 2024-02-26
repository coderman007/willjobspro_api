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
            'name' => $this->name,
            'email' => $this->user->email,
            // 'jobs' => $this->jobs,
            'industry' => $this->industry,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'website' => $this->website,
            'description' => $this->description,
            'contact_person' => $this->contact_person,
            'logo_path' => $this->logo_path,
            'banner_path' => $this->banner_path,
            'social_networks' => $this->social_networks,
            'status' => $this->status,
        ];
    }
}
