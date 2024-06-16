<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 *
 * @property Company|null $company
 * @property Candidate|null $candidate
 * @property mixed $address
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @method static create(array $array)
 * @method static where(string $string, mixed $email)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'zip_code_id',
        'address',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the type of the user (admin, company, candidate).
     *
     * @return string|null
     */
    public function getUserType(): ?string
    {
        // Verificar si el usuario tiene el rol 'admin'
        if ($this->hasRole('admin')) {
            return 'admin';
        }

        // Verificar si el usuario tiene el rol 'company'
        if ($this->hasRole('company')) {
            return 'company';
        }

        // Verificar si el usuario tiene el rol 'candidate'
        if ($this->hasRole('candidate')) {
            return 'candidate';
        }

        // Si no tiene ninguno de los roles anteriores, retorna null o un valor predeterminado según tu lógica
        return null;
    }

    public function candidate():HasOne
    {
        return $this->hasOne(Candidate::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function zipCode(): BelongsTo
    {
        return $this->belongsTo(ZipCode::class);
    }


    public function socialNetworks(): HasMany
    {
        return $this->hasMany(SocialNetwork::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }


}
