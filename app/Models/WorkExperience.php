<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'company',
        'position',
        'responsibility',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Definir la relación con el candidato
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
