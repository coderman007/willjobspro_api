<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'job_category_id' => $this->job_category_id,
            'title' => $this->title,
            // 'subscription_plan_id' => $this->subscription_plan_id,
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

            'company_name' =>  $this->company->name,

            'category_name' => $this->jobCategory->name,

            'job_type_names' => $this->jobTypes->pluck('name')->implode(', '),

            // 'subscription_plan_name' =>  $this->subscriptionPlan->name,

        ];
    }
}
