<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'salesperson',
        'name',
        'email',
        'phone',
        'password',
        'department',
        'position',
        'hire_date',
        'salary',
        'address',
        'is_active',
        'role',
        'team_id',
        'tech_stack_id',
        'sales_leader_id',
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
            'hire_date' => 'date',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the validation rules that apply to the model.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'employee_id' => 'nullable|string|regex:/^[a-zA-Z0-9]{13}$/|unique:users,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'role' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the custom validation messages for the model.
     *
     * @return array<string, string>
     */
    public static function validationMessages(): array
    {
        return [
            'employee_id.regex' => 'Employee ID must be exactly 13 characters (letters or numbers).',
            'employee_id.unique' => 'This employee ID is already in use.',
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters long.',
            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * The sales leader this user sells under (sales members only).
     */
    public function salesLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_leader_id');
    }

    /**
     * The sales members reporting to this user (sales leaders only).
     */
    public function salesMembers(): HasMany
    {
        return $this->hasMany(User::class, 'sales_leader_id');
    }

    /**
     * Orders this user sold.
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'sales_user_id');
    }

    /**
     * Users holding any of the given role names. Unlike Spatie's `role` scope
     * this does not blow up when a role has not been seeded yet.
     *
     * @param  array<int, string>|string  $roles
     */
    public function scopeWithRoleName(Builder $query, array|string $roles): Builder
    {
        return $query->whereHas('roles', fn ($role) => $role->whereIn('name', (array) $roles));
    }

    /**
     * Monthly sales targets set for this user, newest first.
     */
    public function salesTargets(): HasMany
    {
        return $this->hasMany(SalesTarget::class)->orderByDesc('month');
    }

    /**
     * The minimum this person is expected to sell in the given month.
     */
    public function salesTargetFor(?Carbon $month = null): ?float
    {
        return SalesTarget::amountFor($this->id, $month ?? now());
    }

    public function isSales(): bool
    {
        return $this->hasRole(['sales-leader', 'sales-member']);
    }

    public function isSalesLeader(): bool
    {
        return $this->hasRole('sales-leader');
    }

    /**
     * The sales people whose orders this user is allowed to see: their own,
     * plus their whole downline when they lead a sales group.
     *
     * @return array<int, int>
     */
    public function salesVisibleUserIds(): array
    {
        if (! $this->isSalesLeader()) {
            return [$this->id];
        }

        return $this->salesMembers()->pluck('id')->push($this->id)->unique()->values()->all();
    }

    public function techStack(): BelongsTo
    {
        return $this->belongsTo(TechStack::class);
    }

    public function techStacks(): BelongsToMany
    {
        return $this->belongsToMany(TechStack::class, 'tech_stack_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function managedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'manager_team');
    }

    public function isManagerOfTeam(Team $team): bool
    {
        return $this->managedTeams()->where('teams.id', $team->id)->exists();
    }

    public function isManager(): bool
    {
        return $this->hasAnyRole(['admin', 'operation-manager', 'project-manager']);
    }
}
