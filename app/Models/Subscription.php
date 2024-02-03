<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'payment_method',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
