<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'company_id' => $this->company_id,
            'job_category_id' => $this->job_category_id,
            'job_type_id' => $this->job_type_id,
            'title' => $this->title,
            'subscription_plan_id' => $this->subscription_plan_id,
            'description' => $this->description,
            'posted_date' => $this->posted_date,
            'deadline' => $this->deadline,
            'location' => $this->location,
            'salary' => $this->salary,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'company' => [
            //     'id' => $this->company->id,
            //     'user_id' => $this->company->user_id,
            //     'name' => $this->company->name,
            //     'industry' => $this->company->industry,
            //     'address' => $this->company->address,
            //     // ... (incluye los campos necesarios de la compañía)
            // ],
            'job_category' => [
                'id' => $this->jobCategory->id,
                'name' => $this->jobCategory->name,
                // 'description' => $this->jobCategory->description,
                // ... (incluye los campos necesarios de la categoría de trabajo)
            ],
            'job_type' => [
                'id' => $this->jobType->id,
                'name' => $this->jobType->name,
                // 'description' => $this->jobType->description,
                // ... (incluye los campos necesarios del tipo de trabajo)
            ],
            // 'subscription_plan' => [
            //     'id' => $this->subscriptionPlan->id,
            //     'name' => $this->subscriptionPlan->name,
            //     'description' => $this->subscriptionPlan->description,
            //     'price' => $this->subscriptionPlan->price,
            //     'duration' => $this->subscriptionPlan->duration,
            //     // ... (incluye los campos necesarios del plan de suscripción)
            // ],
        ];
    }
}
