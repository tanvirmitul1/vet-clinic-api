<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function owner()
    {
        return $this->hasOne(Owner::class);
    }

    // Helper methods
    public function hasRole($roleName)
    {
        return $this->role->name === $roleName;
    }

    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    public function isDoctor()
    {
        return $this->hasRole('Doctor');
    }

    public function isReceptionist()
    {
        return $this->hasRole('Receptionist');
    }

    public function isPetOwner()
    {
        return $this->hasRole('Pet Owner');
    }
}
