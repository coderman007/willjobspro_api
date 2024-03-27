<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'date_of_birth',
        'phone_number',
        'work_experience',
        'certifications',
        'references',
        'expected_salary',
        'social_networks',
        'status',
        'cv_path',
        'photo_path',
        'banner_path',
    ];

    protected $casts = [
        'social_networks' => 'json',
    ];

    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educationLevels(): BelongsToMany
    {
        return $this->belongsToMany(EducationLevel::class)->withTimestamps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)->withTimestamps();
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class)->withTimestamps();
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // Relación con aplicaciones o postulaciones
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

}
