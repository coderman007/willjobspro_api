<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'date_of_birth',
        'address',
        'phone_number',
        'work_experience',
        'education',
        'skills',
        'certifications',
        'languages',
        'references',
        'expected_salary',
        'cv_path',
        'photo_path',
        'status',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
