<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso_alpha_2',
        'dial_code',
    ];

    // Relación con estados
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    // Relación con usuarios
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Relación con ofertas de trabajo
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}

