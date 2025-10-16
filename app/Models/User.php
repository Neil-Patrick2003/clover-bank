<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    protected $fillable = ['role','username','email','password','status'];



    public function accounts(): HasMany { return $this->hasMany(Account::class); }
    public function applications(): HasMany { return $this->hasMany(CustomerApplication::class); }
    public function assignedApplications(): HasMany { return $this->hasMany(CustomerApplication::class, 'assigned_admin_id'); }
    public function kycProfile(): HasOne { return $this->hasOne(KycProfile::class); }
    public function beneficiaries(): HasMany { return $this->hasMany(Beneficiary::class); }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && $this->status === 'active';
    }

    public function getFilamentName(): string
    {
        // Always return something non-null
        return $this->username ?: ($this->name ?: $this->email);
    }
}
