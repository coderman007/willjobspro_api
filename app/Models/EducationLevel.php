<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EducationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class)->withTimestamps();
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class)->withTimestamps();
    }
}
