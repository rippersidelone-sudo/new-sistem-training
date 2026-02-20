<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    // ===========================================
    // BASIC CONFIGURATION
    // ===========================================

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ===========================================
    // RELATIONSHIPS
    // ===========================================

    /**
     * Users dengan role ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Active users dengan role ini (not soft deleted)
     */
    public function activeUsers(): HasMany
    {
        return $this->hasMany(User::class)->whereNull('deleted_at');
    }

    // ===========================================
    // QUERY SCOPES
    // ===========================================

    /**
     * Scope untuk active roles (not soft deleted)
     * 
     * Usage: Role::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope untuk mencari role by name
     * 
     * Usage: Role::byName('HQ Admin')->first();
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope untuk sorting by name
     * 
     * Usage: Role::sortedByName()->get();
     */
    public function scopeSortedByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Scope untuk role dengan user count
     * 
     * Usage: Role::withUserCount()->get();
     */
    public function scopeWithUserCount($query)
    {
        return $query->withCount([
            'users as total_users',
            'users as active_users' => function($q) {
                $q->whereNull('deleted_at');
            }
        ]);
    }

    /**
     * Scope untuk management roles only
     * (HQ Admin, Training Coordinator, Branch Coordinator)
     * 
     * Usage: Role::managementOnly()->get();
     */
    public function scopeManagementOnly($query)
    {
        return $query->whereIn('name', [
            'HQ Admin',
            'Training Coordinator',
            'Branch Coordinator'
        ]);
    }

    /**
     * Scope untuk non-management roles
     * (Trainer, Participant)
     * 
     * Usage: Role::nonManagement()->get();
     */
    public function scopeNonManagement($query)
    {
        return $query->whereIn('name', [
            'Trainer',
            'Participant'
        ]);
    }

    // ===========================================
    // ACCESSOR METHODS
    // ===========================================

    /**
     * Get total active users count
     */
    public function getActiveUsersCountAttribute(): int
    {
        // Jika sudah loaded via withCount
        if (isset($this->attributes['active_users'])) {
            return $this->attributes['active_users'];
        }
        
        // Fallback ke query
        return $this->activeUsers()->count();
    }

    /**
     * Get total users count (including soft deleted)
     */
    public function getTotalUsersCountAttribute(): int
    {
        // Jika sudah loaded via withCount
        if (isset($this->attributes['total_users'])) {
            return $this->attributes['total_users'];
        }
        
        // Fallback ke query
        return $this->users()->withTrashed()->count();
    }

    // ===========================================
    // ROLE CHECKING METHODS
    // ===========================================

    /**
     * Check if role is HQ Admin
     */
    public function getIsHQAdminAttribute(): bool
    {
        return $this->name === 'HQ Admin';
    }

    /**
     * Check if role is Training Coordinator
     */
    public function getIsCoordinatorAttribute(): bool
    {
        return $this->name === 'Training Coordinator';
    }

    /**
     * Check if role is Branch Coordinator
     */
    public function getIsBranchCoordinatorAttribute(): bool
    {
        return $this->name === 'Branch Coordinator';
    }

    /**
     * Check if role is Trainer
     */
    public function getIsTrainerAttribute(): bool
    {
        return $this->name === 'Trainer';
    }

    /**
     * Check if role is Participant
     */
    public function getIsParticipantAttribute(): bool
    {
        return $this->name === 'Participant';
    }

    /**
     * Check if role has management access
     * (HQ Admin, Training Coordinator, Branch Coordinator)
     */
    public function getHasManagementAccessAttribute(): bool
    {
        return in_array($this->name, [
            'HQ Admin',
            'Training Coordinator',
            'Branch Coordinator'
        ]);
    }

    /**
     * Check if role can manage batches
     * (HQ Admin, Training Coordinator)
     */
    public function getCanManageBatchesAttribute(): bool
    {
        return in_array($this->name, [
            'HQ Admin',
            'Training Coordinator'
        ]);
    }

    /**
     * Check if role can manage participants
     * (HQ Admin, Training Coordinator, Branch Coordinator)
     */
    public function getCanManageParticipantsAttribute(): bool
    {
        return in_array($this->name, [
            'HQ Admin',
            'Training Coordinator',
            'Branch Coordinator'
        ]);
    }

    /**
     * Check if role can teach/train
     * (Trainer)
     */
    public function getCanTeachAttribute(): bool
    {
        return $this->name === 'Trainer';
    }

    /**
     * Check if role is learner
     * (Participant)
     */
    public function getIsLearnerAttribute(): bool
    {
        return $this->name === 'Participant';
    }

    // ===========================================
    // HELPER METHODS
    // ===========================================

    /**
     * Check if role memiliki users
     */
    public function hasUsers(): bool
    {
        return $this->users()->exists();
    }

    /**
     * Check if role memiliki active users
     */
    public function hasActiveUsers(): bool
    {
        return $this->activeUsers()->exists();
    }

    /**
     * Check if role dapat dihapus
     * (tidak bisa dihapus jika masih ada user atau role sistem)
     */
    public function canBeDeleted(): bool
    {
        // System roles cannot be deleted
        $systemRoles = [
            'HQ Admin',
            'Training Coordinator',
            'Branch Coordinator',
            'Trainer',
            'Participant'
        ];

        if (in_array($this->name, $systemRoles)) {
            return false;
        }

        // Cannot delete if has users
        return !$this->hasUsers();
    }

    /**
     * Get badge color untuk UI
     */
    public function getBadgeColor(): string
    {
        return match($this->name) {
            'HQ Admin' => 'bg-blue-100 text-blue-700',
            'Training Coordinator' => 'bg-green-100 text-green-700',
            'Trainer' => 'bg-purple-100 text-purple-700',
            'Branch Coordinator' => 'bg-orange-100 text-orange-700',
            'Participant' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get role icon (optional, untuk keperluan UI)
     */
    public function getIcon(): string
    {
        return match($this->name) {
            'HQ Admin' => 'shield-check',
            'Training Coordinator' => 'calendar',
            'Trainer' => 'presentation',
            'Branch Coordinator' => 'building',
            'Participant' => 'user',
            default => 'user-circle',
        };
    }

    /**
     * Get role priority (untuk sorting)
     * Lower number = higher priority
     */
    public function getPriority(): int
    {
        return match($this->name) {
            'HQ Admin' => 1,
            'Training Coordinator' => 2,
            'Branch Coordinator' => 3,
            'Trainer' => 4,
            'Participant' => 5,
            default => 999,
        };
    }

    /**
     * Get readable description
     */
    public function getReadableDescription(): string
    {
        return $this->description ?? match($this->name) {
            'HQ Admin' => 'Pengelola pusat dan pengendali seluruh sistem',
            'Training Coordinator' => 'Pengatur jadwal, batch, dan peserta pelatihan',
            'Trainer' => 'Pelaksana kegiatan pelatihan',
            'Branch Coordinator' => 'PIC peserta tingkat cabang',
            'Participant' => 'Peserta Pelatihan',
            default => 'Role tanpa deskripsi',
        };
    }

    // ===========================================
    // STATIC HELPER METHODS
    // ===========================================

    /**
     * Get role by name (cached)
     * 
     * Usage: Role::getByName('HQ Admin');
     */
    public static function getByName(string $name): ?self
    {
        return cache()->remember(
            "role.name.{$name}",
            3600, // 1 hour
            fn() => static::where('name', $name)->first()
        );
    }

    /**
     * Get all role names
     * 
     * Usage: Role::getAllNames();
     * Returns: ['1' => 'HQ Admin', '2' => 'Training Coordinator', ...]
     */
    public static function getAllNames(): array
    {
        return cache()->remember(
            'roles.all_names',
            3600, // 1 hour
            fn() => static::pluck('name', 'id')->toArray()
        );
    }

    /**
     * Get roles ordered by hierarchy
     * 
     * Usage: Role::getHierarchical();
     */
    public static function getHierarchical()
    {
        return cache()->remember(
            'roles.hierarchical',
            3600,
            function() {
                $roleOrder = [
                    'HQ Admin',
                    'Training Coordinator',
                    'Branch Coordinator',
                    'Trainer',
                    'Participant'
                ];

                $roles = static::all();
                
                return $roles->sortBy(function($role) use ($roleOrder) {
                    $index = array_search($role->name, $roleOrder);
                    return $index !== false ? $index : 999;
                })->values();
            }
        );
    }

    /**
     * Get management roles only
     * 
     * Usage: Role::getManagementRoles();
     */
    public static function getManagementRoles()
    {
        return cache()->remember(
            'roles.management',
            3600,
            fn() => static::managementOnly()->get()
        );
    }

    /**
     * Get non-management roles only
     * 
     * Usage: Role::getNonManagementRoles();
     */
    public static function getNonManagementRoles()
    {
        return cache()->remember(
            'roles.non_management',
            3600,
            fn() => static::nonManagement()->get()
        );
    }

    /**
     * Check if role name exists
     * 
     * Usage: Role::exists('HQ Admin');
     */
    public static function roleExists(string $name): bool
    {
        return static::where('name', $name)->exists();
    }

    /**
     * Get role ID by name
     * 
     * Usage: Role::getIdByName('HQ Admin');
     */
    public static function getIdByName(string $name): ?int
    {
        $role = static::getByName($name);
        return $role?->id;
    }

    /**
     * Create or get role
     * 
     * Usage: Role::createOrGet('Custom Role', 'Description');
     */
    public static function createOrGet(string $name, ?string $description = null): self
    {
        return static::firstOrCreate(
            ['name' => $name],
            ['description' => $description]
        );
    }

    // ===========================================
    // VALIDATION METHODS
    // ===========================================

    /**
     * Validate if role name is valid system role
     */
    public static function isValidSystemRole(string $name): bool
    {
        $systemRoles = [
            'HQ Admin',
            'Training Coordinator',
            'Branch Coordinator',
            'Trainer',
            'Participant'
        ];

        return in_array($name, $systemRoles);
    }

    /**
     * Get all system role names
     */
    public static function getSystemRoleNames(): array
    {
        return [
            'HQ Admin',
            'Training Coordinator',
            'Branch Coordinator',
            'Trainer',
            'Participant'
        ];
    }

    // ===========================================
    // BOOT METHOD
    // ===========================================

    protected static function boot()
    {
        parent::boot();

        // Clear cache saat role berubah
        static::saved(function ($role) {
            self::clearCache($role->name);
        });

        static::deleted(function ($role) {
            self::clearCache($role->name);
        });

        static::restored(function ($role) {
            self::clearCache($role->name);
        });
    }

    /**
     * Clear all role-related cache
     */
    protected static function clearCache(?string $roleName = null)
    {
        if ($roleName) {
            cache()->forget("role.name.{$roleName}");
        }
        
        cache()->forget('roles.all_names');
        cache()->forget('roles.hierarchical');
        cache()->forget('roles.management');
        cache()->forget('roles.non_management');
    }

    /**
     * Clear all cache manually
     * 
     * Usage: Role::clearAllCache();
     */
    public static function clearAllCache(): void    
    {
        self::clearCache();
    }
}