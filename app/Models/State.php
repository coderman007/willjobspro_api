<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
    ];

    // Relación con el país
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Relación con ciudades
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
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
