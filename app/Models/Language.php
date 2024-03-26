<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'candidate_language')->withTimestamps();
    }
}
