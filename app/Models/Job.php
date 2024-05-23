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

    // Los campos que pueden ser asignados masivamente
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

    // Valores predeterminados de atributos
    protected $attributes = [
        'status' => 'Open',
    ];

    // Relación uno a muchos con Application
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Relación muchos a uno con Company
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Relación muchos a uno con JobCategory
    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    // Relación muchos a muchos con Language
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class)->withPivot('level')->withTimestamps();
    }

    // Relación muchos a muchos con JobType
    public function jobTypes(): BelongsToMany
    {
        return $this->belongsToMany(JobType::class)->withTimestamps();
    }

    // Relación muchos a muchos con Skill
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)->withTimestamps();
    }

    // Relación muchos a muchos con EducationLevel
    public function educationLevels(): BelongsToMany
    {
        return $this->belongsToMany(EducationLevel::class)->withTimestamps();
    }

    // Relación muchos a uno con SubscriptionPlan
//    public function subscriptionPlan(): BelongsTo
//    {
//        return $this->belongsTo(SubscriptionPlan::class);
//    }

    // Relación muchos a uno con Country
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Relación muchos a uno con State
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    // Relación muchos a uno con City
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // Relación muchos a uno con ZipCode
    public function zipCode(): BelongsTo
    {
        return $this->belongsTo(ZipCode::class);
    }

    // Relación muchos a muchos con Benefit, especificando tabla pivote
    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'benefit_job')->withTimestamps();
    }
}
