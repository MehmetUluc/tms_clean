<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
    
    /**
     * Check if user is admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        // User ID 1 is always admin
        if ($this->id === 1) {
            return true;
        }
        
        // User with super_admin role is admin
        if ($this->hasRole('super_admin')) {
            return true;
        }
        
        // User with admin role is admin
        if ($this->hasRole('admin')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user is super admin
     * 
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        // User ID 1 is always super admin
        if ($this->id === 1) {
            return true;
        }
        
        // User with super_admin role is super admin
        if ($this->hasRole('super_admin')) {
            return true;
        }
        
        return false;
    }
}
