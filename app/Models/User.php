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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'must_change_password' => 'boolean',
        ];
    }

    // Role helpers - Métodos auxiliares para verificar papéis do usuário
    public const ROLE_ROOT = 'root';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_HR = 'hr';
    public const ROLE_EMPLOYEE = 'employee';

    /**
     * Verifica se o usuário é ROOT (nível máximo de privilégios)
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->role === UserRole::ROOT;
    }

    /**
     * Verifica se o usuário é ADMIN ou superior (inclui ROOT)
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::ROOT], true);
    }

    /**
     * Verifica se o usuário é HR ou superior (inclui ADMIN e ROOT)
     * @return bool
     */
    public function isHr(): bool
    {
        return in_array($this->role, [UserRole::HR, UserRole::ADMIN, UserRole::ROOT], true);
    }

    /**
     * Verifica se o usuário é EMPLOYEE (nível básico)
     * @return bool
     */
    public function isEmployee(): bool
    {
        return $this->role === UserRole::EMPLOYEE;
    }

    /**
     * Verifica se este usuário tem pelo menos o nível de privilégio de um papel dado
     * @param UserRole $role
     * @return bool
     */
    public function hasPrivilegeOf(UserRole $role): bool
    {
        return $this->role->hasPrivilegeOf($role);
    }

    // RELACIONAMENTOS - Define as relações com outras entidades
    /**
     * Relacionamento um-para-um com Employee
     * Um usuário pode ter um funcionário associado
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Relacionamento um-para-muitos com PerformanceReview
     * Um usuário pode ter várias avaliações de desempenho como revisor
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewer_id');
    }

    /**
     * Relacionamento um-para-muitos com EmployeeBenefit
     * Um usuário pode ter vários benefícios de funcionários aprovados
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvedEmployeeBenefits()
    {
        return $this->hasMany(EmployeeBenefit::class, 'approved_by');
    }

    /**
     * Método executado quando o modelo é inicializado
     * Define eventos para criação e salvamento do usuário
     */
    protected static function booted()
    {
        // Define senha padrão e obrigatoriedade de mudança se não fornecida
        static::creating(function (User $user) {
            if (empty($user->password)) {
                $user->password = Hash::make('passexemplo123');
                $user->must_change_password = true;
            }
        });

        // Após criação, define email_verified_at se não fornecido
        static::created(function (User $user) {
            if (empty($user->email_verified_at)) {
                // Usa saveQuietly para evitar loops de eventos
                $user->email_verified_at = $user->created_at;
                $user->saveQuietly();
            }
        });

        // Previne mudança de email_verified_at uma vez definido
        static::saving(function (User $user) {
            if ($user->exists) {
                $original = $user->getOriginal('email_verified_at');
                if ($original !== null && $user->isDirty('email_verified_at')) {
                    // Reverte qualquer tentativa de mudança do timestamp verificado
                    $user->email_verified_at = $original;
                }
            }
        });
    }

}
