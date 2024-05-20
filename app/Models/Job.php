<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'job_category_id',
        'subscription_plan_id',
        'title',
        'description',
        'posted_date',
        'deadline',
        'country_id',
        'state_id',
        'city_id',
        'zip_code_id',
        'salary',
        'contact_email',
        'contact_phone',
        'experience_required',
        'image',
        'video'
    ];

    protected $attributes = [
        'status' => 'Open',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class)->withPivot('level')->withTimestamps(); // Incluye el campo 'level'
    }

    public function jobTypes(): BelongsToMany
    {
        return $this->belongsToMany(JobType::class)->withTimestamps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)->withTimestamps();
    }

    public function educationLevels(): BelongsToMany
    {
        return $this->belongsToMany(EducationLevel::class)->withTimestamps();
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function zipCode(): BelongsTo
    {
        return $this->belongsTo(ZipCode::class);
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'benefit_job');
    }
}
