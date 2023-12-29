<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'job_category_id',
        'job_type_id',
        'title',
        'description',
        'posted_date',
        'deadline',
        'location',
        'salary',
        'contact_email',
        'contact_phone'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
