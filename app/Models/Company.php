<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'industry',
        'address',
        'phone_number',
        'website',
        'description',
        'contact_person',
        'logo_path',
        'banner_path',
        'social_networks',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'social_networks' => 'json',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relación con las ofertas de trabajo
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
