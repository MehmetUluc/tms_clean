<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'password',
        'api_key',
        'description',
        'company_name',
        'contact_email',
        'contact_phone',
        'allowed_ips',
        'permissions',
        'is_active',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'api_key',
    ];

    protected $casts = [
        'allowed_ips' => 'array',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function mappings(): HasMany
    {
        return $this->hasMany(ApiMapping::class);
    }

    public function setPasswordAttribute($value)
    {
        // Hash password if it's not already hashed
        $this->attributes['password'] = (strlen($value) === 60 && preg_match('/^\$2y\$/', $value))
            ? $value
            : Hash::make($value);
    }

    public function generateApiKey()
    {
        $this->api_key = Str::random(64);
        return $this->api_key;
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    public function checkIpAllowed(?string $ip): bool
    {
        if (!$this->allowed_ips || empty($this->allowed_ips)) {
            return true; // No IP restrictions means all IPs are allowed
        }

        if (!$ip) {
            return false;
        }

        return in_array($ip, $this->allowed_ips) || in_array('*', $this->allowed_ips);
    }
}
