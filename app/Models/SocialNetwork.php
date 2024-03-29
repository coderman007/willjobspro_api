<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
    ];

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withTimestamps();
    }
}
