<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'job_category_id' => $this->job_category_id,
            'education_level_id' => $this->education_level_id,
            'title' => $this->title,
            'description' => $this->description,
            'posted_date' => $this->posted_date,
            'deadline' => $this->deadline,
            'location' => $this->location,
            'salary' => $this->salary,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'min_experience_required' => $this->experience_required,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'company_name' =>  $this->company->name,

            'category_name' => $this->jobCategory->name,

            'education_level_name' => $this->educationLevel->name,

            'job_type_names' => $this->jobTypes->pluck('name')->implode(', '),

            // Nuevo campo para indicar si el candidato ha aplicado a la oferta
            'applied' => $this->when(isset($this->applied), $this->applied),
        ];
    }
}
