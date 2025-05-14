<?php

namespace App\Plugins\UserManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Plugins\Core\src\Traits\HasTenant;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'country',
        'is_active',
        'last_login_at',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];
    
    /**
     * Kullanıcı profil fotoğrafı URL'si
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo_path) {
            return null;
        }
        
        return filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)
            ? $this->profile_photo_path
            : '/storage/' . $this->profile_photo_path;
    }
    
    /**
     * Kullanıcının tam adresini döndürür
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->country,
        ]);
        
        return implode(', ', $parts);
    }
    
    /**
     * Kullanıcının super-admin olup olmadığını döndürür
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
    
    /**
     * Kullanıcının admin olup olmadığını döndürür
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->isSuperAdmin();
    }
}