<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_category_id',
        'name',
        'description',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function skillCategory()
    {
        return $this->belongsTo(SkillCategory::class);
    }

    public function candidates()
    {
        return $this->belongsToMany(Candidate::class);
    }
}
