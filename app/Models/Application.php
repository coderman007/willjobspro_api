<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'job_id',
        'cover_letter',
        'application_date',
        'rejection_date',
    ];

    protected $attributes = [
        'status' => 'Pending',
    ];

    protected array $dates = [
        'application_date',
        'rejection_date',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
