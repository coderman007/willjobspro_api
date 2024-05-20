<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_person',
        'phone_number',
        'industry',
        'description',
        'website',
        'logo',
        'banner',
        'video'
    ];

    protected $attributes = [
        'status' => 'Active',
    ];


    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación con las ofertas de trabajo
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

}
