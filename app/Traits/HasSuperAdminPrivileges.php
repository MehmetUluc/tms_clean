<?php

namespace App\Traits;

trait HasSuperAdminPrivileges
{
    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        // Check by ID first to avoid any role checking loops
        if ($this->id === 1) {
            return true;
        }
        
        try {
            // Use the original Spatie method directly to avoid infinite loops
            return $this->originalHasRole('super_admin') || $this->originalHasRole('super-admin');
        } catch (\Exception $e) {
            // B2C users might not have any roles, return false
            return false;
        }
    }
    /**
     * Override Spatie's hasPermissionTo method
     * Super admin always has all permissions
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        try {
            return $this->originalHasPermissionTo($permission, $guardName);
        } catch (\Exception $e) {
            // B2C users might not have any permissions, return false
            return false;
        }
    }

    /**
     * Override Laravel's can method
     * Super admin can do everything
     */
    public function can($abilities, $arguments = []): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Call parent's can method from Illuminate\Foundation\Auth\User
        return parent::can($abilities, $arguments);
    }

    /**
     * Override Spatie's hasRole method
     * Super admin has all roles implicitly (except some special cases)
     */
    public function hasRole($roles, string $guard = null): bool
    {
        // Super admin should NOT have these specific roles
        $excludedRoles = ['partner', 'partner_staff'];
        
        if ($this->isSuperAdmin()) {
            // If checking for excluded roles, return false
            if (is_string($roles) && in_array($roles, $excludedRoles)) {
                return false;
            }
            
            // If checking for array of roles, check if any is excluded
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    if (in_array($role, $excludedRoles)) {
                        return false;
                    }
                }
            }
            
            // For all other roles, super admin has them
            return true;
        }

        try {
            return $this->originalHasRole($roles, $guard);
        } catch (\Exception $e) {
            // B2C users might not have any roles, return false
            return false;
        }
    }

    /**
     * Override Spatie's hasAnyRole method
     * Super admin has all roles implicitly (except some special cases)
     */
    public function hasAnyRole(...$roles): bool
    {
        $excludedRoles = ['partner', 'partner_staff'];
        
        if ($this->isSuperAdmin()) {
            // Flatten the roles array in case of nested arrays
            $flatRoles = collect($roles)->flatten()->toArray();
            
            // If any of the roles is excluded, return false
            foreach ($flatRoles as $role) {
                if (in_array($role, $excludedRoles)) {
                    return false;
                }
            }
            
            // For all other roles, super admin has them
            return true;
        }

        try {
            return $this->originalHasAnyRole(...$roles);
        } catch (\Exception $e) {
            // B2C users might not have any roles, return false
            return false;
        }
    }

    /**
     * Override Spatie's hasAllRoles method
     * Super admin has all roles implicitly
     */
    public function hasAllRoles($roles, string $guard = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        try {
            return $this->originalHasAllRoles($roles, $guard);
        } catch (\Exception $e) {
            // B2C users might not have any roles, return false
            return false;
        }
    }
}