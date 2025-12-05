<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get all permissions through roles.
     */
    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            Role::class,
            'id',
            'id',
            'user_roles.user_id',
            'role_id'
        );
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole($roleNames)
    {
        return $this->roles()->whereIn('name', (array) $roleNames)->exists();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles($roleNames)
    {
        $roleNames = (array) $roleNames;
        return $this->roles()->whereIn('name', $roleNames)->count() === count($roleNames);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permissionName)
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }

    /**
     * Get stock items created by user.
     */
    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }

    /**
     * Get stock movements made by user.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

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
}
