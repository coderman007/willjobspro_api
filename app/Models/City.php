<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        // Otros campos necesarios
    ];

    // Relación con el estado
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    // Relación con códigos postales
    public function zipCodes(): HasMany
    {
        return $this->hasMany(ZipCode::class);
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

