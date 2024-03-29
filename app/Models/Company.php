<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'status',
        'logo_file',
        'banner_file',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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

    public function socialNetworks(): BelongsToMany
    {
        return $this->belongsToMany(SocialNetwork::class)->withTimestamps();
    }
}
