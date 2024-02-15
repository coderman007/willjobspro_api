<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'gender',
        'date_of_birth',
        'address',
        'phone_number',
        'work_experience',
        'education',
        'certifications',
        'languages',
        'references',
        'expected_salary',
        'cv_file',
        'photo_file',
        'banner_file',
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

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    // Relación con aplicaciones o postulaciones
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Agregar habilidad al candidato

    public function addSkill($skillId)
    {
        $this->skills()->attach($skillId);
    }

    // Quitar habilidad al candidato
    public function removeSkill($skillId)
    {
        $this->skills()->detach($skillId);
    }
}
