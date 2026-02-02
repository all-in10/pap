<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'role',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    // Role helpers
    public const ROLE_ROOT = 'root';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_HR = 'hr';
    public const ROLE_EMPLOYEE = 'employee';

    public function isRoot(): bool
    {
        return $this->role === UserRole::ROOT;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::ROOT], true);
    }

    public function isHr(): bool
    {
        return in_array($this->role, [UserRole::HR, UserRole::ADMIN, UserRole::ROOT], true);
    }

    public function isEmployee(): bool
    {
        return $this->role === UserRole::EMPLOYEE;
    }

    /**
     * Return the canonical panel path for this user's role.
     * Used as a safe redirect target after login.
     */
    public function panelPath(): string
    {
        return match ($this->role) {
            UserRole::ROOT, UserRole::ADMIN => '/admin',
            UserRole::HR => '/hr',
            UserRole::EMPLOYEE => '/employee',
            default => '/',
        };
    }

    /**
     * Check whether this user is allowed to access the given panel slug.
     * Accepts the first segment of the request path (e.g. 'admin', 'hr', 'employee').
     */
    public function canAccessPanel(string $panel): bool
    {
        // Root should only have access to the admin panel. Use explicit role checks
        // to avoid helper methods that implicitly include root/admin together.
        return match ($panel) {
            'admin' => $this->role === UserRole::ADMIN || $this->role === UserRole::ROOT,
            'hr' => $this->role === UserRole::HR || $this->role === UserRole::ADMIN,
            'employee' => $this->role === UserRole::EMPLOYEE,
            'filament' => $this->role === UserRole::ADMIN || $this->role === UserRole::HR,
            default => false,
        };
    }

    /**
     * Check if this user has at least the privilege level of a given role
     */
    public function hasPrivilegeOf(UserRole $role): bool
    {
        return $this->role->hasPrivilegeOf($role);
    }

    // RELACIONAMENTOS
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    protected static function booted()
    {
        // Set default password and must_change_password if not provided
        static::creating(function (User $user) {
            if (empty($user->password)) {
                $user->password = Hash::make('passexemplo123');
                $user->must_change_password = true;
            }
        });

        // After creation, ensure email_verified_at is set to created_at if not provided
        static::created(function (User $user) {
            if (empty($user->email_verified_at)) {
                // Use saveQuietly to avoid triggering event loops
                $user->email_verified_at = $user->created_at;
                $user->saveQuietly();
            }
        });

        // Prevent changing email_verified_at once it's set
        static::saving(function (User $user) {
            if ($user->exists) {
                $original = $user->getOriginal('email_verified_at');
                if ($original !== null && $user->isDirty('email_verified_at')) {
                    // Revert any attempt to change the verified timestamp
                    $user->email_verified_at = $original;
                }
            }
        });
    }

}
