<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasSuperAdminPrivileges;
use App\Plugins\Agency\Models\Agency;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasSuperAdminPrivileges {
        HasSuperAdminPrivileges::hasPermissionTo insteadof HasRoles;
        HasSuperAdminPrivileges::hasRole insteadof HasRoles;
        HasSuperAdminPrivileges::hasAnyRole insteadof HasRoles;
        HasSuperAdminPrivileges::hasAllRoles insteadof HasRoles;
        
        // Keep the original methods available with different names if needed
        HasRoles::hasPermissionTo as originalHasPermissionTo;
        HasRoles::hasRole as originalHasRole;
        HasRoles::hasAnyRole as originalHasAnyRole;
        HasRoles::hasAllRoles as originalHasAllRoles;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',
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
        
        // Try to check roles, but catch any exceptions for B2C users
        try {
            // User with super_admin role is admin
            if ($this->originalHasRole('super_admin')) {
                return true;
            }
            
            // User with admin role is admin
            if ($this->originalHasRole('admin')) {
                return true;
            }
        } catch (\Exception $e) {
            // B2C users might not have any roles
            return false;
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
        
        // Try to check roles, but catch any exceptions for B2C users
        try {
            // Check both variations of super admin role
            if ($this->originalHasRole('super_admin') || $this->originalHasRole('super-admin')) {
                return true;
            }
        } catch (\Exception $e) {
            // B2C users might not have any roles
            return false;
        }
        
        return false;
    }
    
    /**
     * Check if user is partner
     * 
     * @return bool
     */
    public function isPartner(): bool
    {
        // Super admin is never a partner
        if ($this->id === 1) {
            return false;
        }
        
        // Check if roles are loaded to avoid N+1 queries
        try {
            return $this->roles()->where('name', 'partner')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Check if user is partner staff
     * 
     * @return bool
     */
    public function isPartnerStaff(): bool
    {
        // Super admin is never partner staff
        if ($this->id === 1) {
            return false;
        }
        
        // Check if roles are loaded to avoid N+1 queries
        try {
            return $this->roles()->where('name', 'partner_staff')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get the partner record associated with this user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partner()
    {
        return $this->hasOne(\App\Plugins\Partner\Models\Partner::class);
    }
    
    /**
     * Get the partner this user belongs to (for staff)
     * 
     * @return \App\Plugins\Partner\Models\Partner|null
     */
    public function getAssociatedPartner()
    {
        try {
            // B2C users don't have partners - check using isPartner/isPartnerStaff to avoid loops
            if (!$this->isPartner() && !$this->isPartnerStaff()) {
                return null;
            }
            
            // Partner role - direct relationship
            if ($this->isPartner()) {
                return $this->partner()->first();
            }
            
            // Partner staff - find through staff relationship
            if ($this->isPartnerStaff()) {
                return \App\Plugins\Partner\Models\Partner::whereHas('staff', function ($query) {
                    $query->where('user_id', $this->id);
                })->first();
            }
        } catch (\Exception $e) {
            // Any error, return null
            return null;
        }
        
        return null;
    }
    
    /**
     * Get the agency this user belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    
    /**
     * Check if user is agency staff
     * 
     * @return bool
     */
    public function isAgencyStaff(): bool
    {
        // Check if user has an agency first
        if ($this->agency_id === null) {
            return false;
        }
        
        // Try to check role, handle B2C users who might not have any roles
        try {
            // Use the original method to avoid loops
            return $this->originalHasRole('agency_staff');
        } catch (\Exception $e) {
            // If user doesn't have any roles (B2C user), return false
            return false;
        }
    }
    
    /**
     * Check if user is agency owner
     * 
     * @return bool
     */
    public function isAgencyOwner(): bool
    {
        if (!$this->agency_id) {
            return false;
        }
        
        return $this->agency->owner_id === $this->id;
    }
}
