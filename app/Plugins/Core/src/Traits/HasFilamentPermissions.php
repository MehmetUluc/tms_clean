<?php

namespace App\Plugins\Core\src\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasFilamentPermissions
{
    /**
     * Get the permission name for viewing any records
     */
    protected static function getViewAnyPermission(): ?string
    {
        return static::$viewAnyPermission ?? null;
    }
    
    /**
     * Get the permission name for viewing a specific record
     */
    protected static function getViewPermission(): ?string
    {
        return static::$viewPermission ?? null;
    }
    
    /**
     * Get the permission name for creating records
     */
    protected static function getCreatePermission(): ?string
    {
        return static::$createPermission ?? null;
    }
    
    /**
     * Get the permission name for updating records
     */
    protected static function getUpdatePermission(): ?string
    {
        return static::$updatePermission ?? null;
    }
    
    /**
     * Get the permission name for deleting records
     */
    protected static function getDeletePermission(): ?string
    {
        return static::$deletePermission ?? null;
    }
    
    /**
     * Check if user can access the resource
     */
    public static function canAccess(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $permission = static::getViewAnyPermission();
        
        if ($permission === null) {
            return true; // No permission required
        }
        
        return Auth::user()->can($permission);
    }
    
    /**
     * Check if user can view any records
     */
    public static function canViewAny(): bool
    {
        return static::canAccess();
    }
    
    /**
     * Check if user can view a specific record
     */
    public static function canView(Model $record): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $permission = static::getViewPermission();
        
        if ($permission === null) {
            return true; // No permission required
        }
        
        return Auth::user()->can($permission);
    }
    
    /**
     * Check if user can create records
     */
    public static function canCreate(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $permission = static::getCreatePermission();
        
        if ($permission === null) {
            return true; // No permission required
        }
        
        return Auth::user()->can($permission);
    }
    
    /**
     * Check if user can edit a record
     */
    public static function canEdit(Model $record): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $permission = static::getUpdatePermission();
        
        if ($permission === null) {
            return true; // No permission required
        }
        
        return Auth::user()->can($permission);
    }
    
    /**
     * Check if user can delete a record
     */
    public static function canDelete(Model $record): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $permission = static::getDeletePermission();
        
        if ($permission === null) {
            return true; // No permission required
        }
        
        return Auth::user()->can($permission);
    }
    
    /**
     * Check if user can bulk delete records
     */
    public static function canDeleteAny(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $permission = static::getDeletePermission();
        
        if ($permission === null) {
            return true; // No permission required
        }
        
        return Auth::user()->can($permission);
    }
}