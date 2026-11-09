<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'institution',
        'latitude',
        'longitude',
        'is_verified',
        'avatar',
    ];

    /**
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
            'latitude' => 'float',
            'longitude' => 'float',
            'is_verified' => 'boolean',
        ];
    }

    public function petaniProfile(): HasOne
    {
        return $this->hasOne(PetaniProfile::class, 'user_id');
    }

    public function pembeliProfile(): HasOne
    {
        return $this->hasOne(PembeliProfile::class, 'user_id');
    }

    public function rencanaPanen(): HasMany
    {
        return $this->hasMany(RencanaPanen::class, 'petani_id');
    }

    public function kebutuhanPembeli(): HasMany
    {
        return $this->hasMany(KebutuhanPembeli::class, 'pembeli_id');
    }

    public function penawaranSebagaiPetani(): HasMany
    {
        return $this->hasMany(Penawaran::class, 'petani_id');
    }

    public function penawaranSebagaiPembeli(): HasMany
    {
        return $this->hasMany(Penawaran::class, 'pembeli_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetani(): bool
    {
        return $this->role === 'petani';
    }

    public function isPembeli(): bool
    {
        return in_array($this->role, ['pedagang', 'umkm'], true);
    }
}
