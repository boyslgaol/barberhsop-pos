<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_KASIR = 'cashier';
    const ROLE_OWNER = 'barber';

    const ROLES = [
        self::ROLE_ADMIN => 'Administrator',
        self::ROLE_KASIR => 'Kasir',
        self::ROLE_OWNER => 'Barber',
    ];

    // Role colors for badge
    const ROLE_COLORS = [
        self::ROLE_ADMIN => 'danger',
        self::ROLE_KASIR => 'primary',
        self::ROLE_OWNER => 'info',
    ];

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is kasir
     */
    public function isKasir()
    {
        return $this->role === self::ROLE_KASIR;
    }

    /**
     * Check if user is owner
     */
    public function isOwner()
    {
        return $this->role === self::ROLE_OWNER;
    }

    /**
     * Get role badge class
     */
    public function getRoleBadgeClass()
    {
        return self::ROLE_COLORS[$this->role] ?? 'secondary';
    }

    /**
     * Get role name
     */
    public function getRoleNameAttribute()
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Scope for active users only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users only
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        // Generate gravatar or default avatar
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp";
    }

    /**
     * Update last login info
     */
    public function updateLastLogin($ip = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }
}