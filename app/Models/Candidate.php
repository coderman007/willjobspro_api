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
        'full_name',
        'gender',
        'date_of_birth',
        'phone_number',
        'work_experience',
        'certifications',
        'references',
        'expected_salary',
        'cv_path',
        'photo_path',
        'banner_path',
        'social_networks',
        'status',
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

    // Agregar habilidad al candidato
    public function addSkill($skillId): void
    {
        $this->skills()->attach($skillId);
    }

    // Quitar habilidad al candidato
    public function removeSkill($skillId): void
    {
        $this->skills()->detach($skillId);
    }

    // Agregar nivel académico al candidato
    public function addEducationLevel($educationLevelId): void
    {
        $this->educationLevels()->attach($educationLevelId);
    }

    // Quitar nivel académico al candidato
    public function removeEducationLevel($educationLevelId): void
    {
        $this->educationLevels()->detach($educationLevelId);
    }

    // Agregar idioma al candidato
    public function addLanguage($languageId): void
    {
        $this->languages()->attach($languageId);
    }

    // Quitar idioma al candidato
    public function removeLanguage($languageId): void
    {
        $this->languages()->detach($languageId);
    }
}
