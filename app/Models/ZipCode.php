<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZipCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'code',
    ];

    // Relación con la ciudad
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
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

